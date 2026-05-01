<?php

declare(strict_types=1);

namespace App\Services\Inventario;

use App\Enums\MovementType;
use App\Models\InventoryMovement;
use App\Models\Medication;
use App\Services\Bitacora\Contracts\BitacoraServiceInterface;
use App\Services\Inventario\Contracts\StockServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class StockService implements StockServiceInterface
{
    public function __construct(
        private readonly BitacoraServiceInterface $bitacora,
    ) {}

    public function ajustar(Medication $medication, array $data): InventoryMovement
    {
        $type = MovementType::from($data['type']);
        $quantity = (int) $data['quantity'];
        $reason = (string) $data['reason'];

        $movement = DB::transaction(function () use ($medication, $type, $quantity, $reason): InventoryMovement {
            $locked = Medication::query()
                ->whereKey($medication->id)
                ->lockForUpdate()
                ->firstOrFail();

            $stockBefore = (int) $locked->stock;
            $stockAfter = $stockBefore + $quantity;

            if ($stockAfter < 0) {
                throw new \DomainException(
                    'El stock resultante no puede ser negativo. Stock actual: '.$stockBefore.'.'
                );
            }

            $locked->stock = $stockAfter;
            $locked->save();

            return InventoryMovement::create([
                'medication_id' => $locked->id,
                'type' => $type->value,
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'user_id' => Auth::id(),
                'reason' => $reason,
            ]);
        });

        $this->bitacora->log('AJUSTE_STOCK', Auth::id(), 'inventory_movements', (string) $movement->id, [
            'medication_id' => $movement->medication_id,
            'type' => $type->value,
            'quantity' => $quantity,
            'reason' => $reason,
        ]);

        return $movement;
    }
}
