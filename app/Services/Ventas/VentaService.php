<?php

declare(strict_types=1);

namespace App\Services\Ventas;

use App\Enums\MovementType;
use App\Enums\PrescriptionStatus;
use App\Enums\SaleStatus;
use App\Models\Medication;
use App\Models\Prescription;
use App\Models\Sale;
use App\Models\SalePrescription;
use App\Services\Bitacora\Contracts\BitacoraServiceInterface;
use App\Services\Inventario\Contracts\MovimientoServiceInterface;
use App\Services\Ventas\Contracts\VentaServiceInterface;
use DomainException;
use Illuminate\Support\Facades\DB;

final class VentaService implements VentaServiceInterface
{
    public function __construct(
        private readonly BitacoraServiceInterface $bitacora,
        private readonly MovimientoServiceInterface $movimientos,
    ) {}

    public function registerSale(array $data, int $salespersonId): Sale
    {
        $items = $this->mergeDuplicateItems($data['items'] ?? []);

        if ($items === []) {
            throw new DomainException('La venta debe tener al menos un producto.');
        }

        $taxRate = (float) setting('tasa_iva', 0.13);
        $allowAnonymous = (bool) setting('permite_venta_sin_cliente', true);

        $customerId = $data['customer_id'] ?? null;
        if ($customerId === null && ! $allowAnonymous) {
            throw new DomainException('El cliente es obligatorio según la configuración del sistema.');
        }

        $sale = DB::transaction(function () use ($items, $data, $salespersonId, $taxRate, $customerId): Sale {
            $resolved = [];
            $subtotal = 0.0;
            $requiresPharmacist = false;

            foreach ($items as $line) {
                /** @var Medication $med */
                $med = Medication::query()
                    ->whereKey((int) $line['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $quantity = (int) $line['quantity'];

                if ($med->stock < $quantity) {
                    throw new DomainException("Stock insuficiente para: {$med->name}");
                }

                $unitPrice = (float) $med->price;
                $lineTotal = round($unitPrice * $quantity, 2);
                $subtotal += $lineTotal;

                if ($med->category->requiresPrescription()) {
                    $requiresPharmacist = true;
                }

                $resolved[] = [
                    'medication' => $med,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ];
            }

            if ($requiresPharmacist) {
                $doctorName = trim((string) ($data['doctor_name'] ?? ''));
                $doctorLicense = trim((string) ($data['doctor_license'] ?? ''));

                if ($doctorName === '' || $doctorLicense === '') {
                    throw new DomainException('La receta requiere nombre y licencia del médico.');
                }
            }

            $subtotal = round($subtotal, 2);
            $tax = round($subtotal * $taxRate, 2);
            $total = round($subtotal + $tax, 2);

            $sale = Sale::create([
                'sold_at' => $data['sold_at'] ?? now(),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'payment_method' => 'cash',
                'status' => $requiresPharmacist ? SaleStatus::PENDING : SaleStatus::COMPLETED,
                'customer_id' => $customerId,
                'salesperson_id' => $salespersonId,
            ]);

            foreach ($resolved as $row) {
                /** @var Medication $med */
                $med = $row['medication'];

                $sale->items()->create([
                    'medication_id' => $med->id,
                    'quantity' => $row['quantity'],
                    'unit_price' => $row['unit_price'],
                    'line_total' => $row['line_total'],
                ]);

                if ($med->category->requiresPrescription()) {
                    $prescription = Prescription::create([
                        'prescription_number' => 'RX-'.strtoupper(bin2hex(random_bytes(4))),
                        'patient_name' => $sale->customer?->name ?? 'Cliente anónimo',
                        'doctor_name' => $data['doctor_name'],
                        'doctor_license' => $data['doctor_license'],
                        'status' => PrescriptionStatus::PENDING,
                        'issued_at' => now(),
                        'expires_at' => now()->addDays((int) setting('dias_validez_receta', 30)),
                        'medication_id' => $med->id,
                    ]);

                    SalePrescription::create([
                        'sale_id' => $sale->id,
                        'prescription_id' => $prescription->id,
                        'medication_id' => $med->id,
                    ]);
                }
            }

            // Stock is only decremented for non-prescription sales.
            // Prescription-gated sales defer the decrement to completeSale().
            if (! $requiresPharmacist) {
                foreach ($resolved as $row) {
                    $row['medication']->decrement('stock', $row['quantity']);
                }

                $this->movimientos->recordSaleMovements(
                    $sale->fresh(['items']),
                    $salespersonId,
                    MovementType::SALE_OUT,
                    "Venta #{$sale->id}"
                );
            }

            return $sale;
        });

        $this->bitacora->log('VENTA_REGISTRADA', $salespersonId, 'sales', (string) $sale->id, [
            'total' => (float) $sale->total,
            'items' => count($items),
            'requires_pharmacist' => $sale->status === SaleStatus::PENDING,
        ]);

        return $sale->fresh(['items', 'prescriptions']);
    }

    public function completeSale(Sale $sale, int $pharmacistId): Sale
    {
        $completed = DB::transaction(function () use ($sale, $pharmacistId): Sale {
            /** @var Sale $fresh */
            $fresh = Sale::query()
                ->whereKey($sale->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($fresh->status !== SaleStatus::PENDING) {
                return $fresh;
            }

            $hasPendingPrescriptions = $fresh->prescriptions()
                ->whereHas('prescription', fn ($q) => $q->where('status', '!=', PrescriptionStatus::VALIDATED->value))
                ->exists();

            if ($hasPendingPrescriptions) {
                throw new DomainException('Aún hay recetas pendientes de validación para esta venta.');
            }

            foreach ($fresh->items as $item) {
                /** @var Medication $med */
                $med = Medication::query()
                    ->whereKey($item->medication_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($med->stock < $item->quantity) {
                    throw new DomainException("Stock insuficiente para: {$med->name}. No se puede completar la venta.");
                }

                $med->decrement('stock', $item->quantity);
            }

            $this->movimientos->recordSaleMovements(
                $fresh,
                $pharmacistId,
                MovementType::SALE_OUT,
                "Venta #{$fresh->id} aprobada por farmacéutico"
            );

            $fresh->update(['status' => SaleStatus::COMPLETED]);

            return $fresh;
        });

        $this->bitacora->log('VENTA_COMPLETADA', $pharmacistId, 'sales', (string) $completed->id, [
            'sale_id' => $completed->id,
            'pharmacist_id' => $pharmacistId,
        ]);

        return $completed->fresh();
    }

    public function cancelSale(Sale $sale, string $reason, int $userId): Sale
    {
        $cancelled = DB::transaction(function () use ($sale, $reason, $userId): Sale {
            /** @var Sale $fresh */
            $fresh = Sale::query()
                ->whereKey($sale->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($fresh->status === SaleStatus::CANCELLED) {
                throw new DomainException('Esta venta ya fue anulada.');
            }

            $wasCompleted = $fresh->status === SaleStatus::COMPLETED;

            // Only restore stock if the sale had already decremented it (COMPLETED state).
            // PENDING sales never had their stock decremented.
            if ($wasCompleted) {
                foreach ($fresh->items as $item) {
                    $item->medication->increment('stock', $item->quantity);
                }
            }

            foreach ($fresh->prescriptions as $salePrescription) {
                if ($salePrescription->prescription) {
                    $salePrescription->prescription->update([
                        'status' => PrescriptionStatus::REJECTED,
                        'notes' => 'Venta anulada: '.$reason,
                    ]);
                }
            }

            $fresh->update([
                'status' => SaleStatus::CANCELLED,
                'cancellation_reason' => $reason,
            ]);

            if ($wasCompleted) {
                $this->movimientos->recordSaleMovements(
                    $fresh,
                    $userId,
                    MovementType::CUSTOMER_RETURN,
                    "Anulación: {$reason}"
                );
            }

            return $fresh;
        });

        $this->bitacora->log('VENTA_CANCELADA', $userId, 'sales', (string) $cancelled->id, [
            'reason' => $reason,
        ]);

        return $cancelled->fresh();
    }

    public function rejectSaleByPharmacist(Sale $sale, string $reason, int $pharmacistId): Sale
    {
        return $this->cancelSale($sale, "Rechazado por farmacéutico: {$reason}", $pharmacistId);
    }

    /**
     * Combine items that target the same product so the
     * unique(sale_id, medication_id) constraint on sale_items is respected.
     */
    private function mergeDuplicateItems(array $items): array
    {
        $merged = [];

        foreach ($items as $line) {
            $productId = (int) ($line['product_id'] ?? 0);
            $quantity = (int) ($line['quantity'] ?? 0);

            if ($productId <= 0 || $quantity <= 0) {
                continue;
            }

            if (isset($merged[$productId])) {
                $merged[$productId]['quantity'] += $quantity;
            } else {
                $merged[$productId] = ['product_id' => $productId, 'quantity' => $quantity];
            }
        }

        return array_values($merged);
    }
}
