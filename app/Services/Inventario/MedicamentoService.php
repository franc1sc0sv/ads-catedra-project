<?php

declare(strict_types=1);

namespace App\Services\Inventario;

use App\Enums\MovementType;
use App\Enums\SaleStatus;
use App\Models\InventoryMovement;
use App\Models\Medication;
use App\Models\Sale;
use App\Services\Inventario\Contracts\MedicamentoServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class MedicamentoService implements MedicamentoServiceInterface
{
    public function listar(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Medication::query()->with('supplier');

        if (! empty($filters['search'])) {
            $term = '%'.$filters['search'].'%';
            $query->where(function ($q) use ($term): void {
                $q->where('name', 'like', $term)
                    ->orWhere('barcode', 'like', $term);
            });
        }

        if (! empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (! empty($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        if (array_key_exists('is_active', $filters) && $filters['is_active'] !== null && $filters['is_active'] !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        return $query->orderBy('name')->paginate($perPage)->withQueryString();
    }

    public function crear(array $data): Medication
    {
        return DB::transaction(function () use ($data): Medication {
            $stockInicial = (int) ($data['stock_inicial'] ?? 0);

            $medication = Medication::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'barcode' => $data['barcode'],
                'price' => $data['price'],
                'stock' => $stockInicial,
                'min_stock' => $data['min_stock'] ?? 0,
                'expires_at' => $data['expires_at'],
                'category' => $data['category'],
                'supplier_id' => $data['supplier_id'],
                'is_active' => array_key_exists('is_active', $data) ? (bool) $data['is_active'] : true,
            ]);

            if ($stockInicial > 0) {
                InventoryMovement::create([
                    'medication_id' => $medication->id,
                    'type' => MovementType::MANUAL_ADJUST->value,
                    'quantity' => $stockInicial,
                    'stock_before' => 0,
                    'stock_after' => $stockInicial,
                    'user_id' => Auth::id(),
                    'reason' => 'Stock inicial al crear el medicamento',
                ]);
            }

            return $medication;
        });
    }

    public function actualizar(Medication $medication, array $data): Medication
    {
        // Stock cambia solo vía movimientos. Removemos la llave por seguridad.
        unset($data['stock'], $data['stock_inicial']);

        $medication->fill([
            'name' => $data['name'] ?? $medication->name,
            'description' => $data['description'] ?? $medication->description,
            'barcode' => $data['barcode'] ?? $medication->barcode,
            'price' => $data['price'] ?? $medication->price,
            'min_stock' => $data['min_stock'] ?? $medication->min_stock,
            'expires_at' => $data['expires_at'] ?? $medication->expires_at,
            'category' => $data['category'] ?? $medication->category,
            'supplier_id' => $data['supplier_id'] ?? $medication->supplier_id,
        ]);

        if (array_key_exists('is_active', $data)) {
            $medication->is_active = (bool) $data['is_active'];
        }

        $medication->save();

        return $medication;
    }

    public function desactivar(Medication $medication): Medication
    {
        return DB::transaction(function () use ($medication): Medication {
            $locked = Medication::query()
                ->whereKey($medication->id)
                ->lockForUpdate()
                ->firstOrFail();

            $hasInProgressSale = Sale::query()
                ->where('status', SaleStatus::IN_PROGRESS->value)
                ->whereHas('items', function ($q) use ($locked): void {
                    $q->where('medication_id', $locked->id);
                })
                ->exists();

            if ($hasInProgressSale) {
                throw new \DomainException(
                    'No se puede desactivar: hay ventas en proceso con este medicamento.'
                );
            }

            $locked->is_active = false;
            $locked->save();

            return $locked;
        });
    }

    public function reactivar(Medication $medication): Medication
    {
        $medication->is_active = true;
        $medication->save();

        return $medication;
    }

    public function estaVencido(Medication $medication): bool
    {
        return $medication->expires_at !== null
            && Carbon::parse($medication->expires_at)->lt(Carbon::today());
    }
}
