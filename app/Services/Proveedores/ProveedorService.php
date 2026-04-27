<?php

declare(strict_types=1);

namespace App\Services\Proveedores;

use App\Models\Supplier;
use App\Services\Proveedores\Contracts\ProveedorServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use RuntimeException;

final class ProveedorService implements ProveedorServiceInterface
{
    public function list(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        return Supplier::query()
            ->when($search !== null && $search !== '', function ($query) use ($search): void {
                $query->where(function ($q) use ($search): void {
                    $q->where('company_name', 'ILIKE', "%{$search}%")
                        ->orWhere('tax_id', 'ILIKE', "%{$search}%");
                });
            })
            ->orderBy('company_name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): Supplier
    {
        return Supplier::create([
            'company_name' => $data['company_name'],
            'tax_id' => $data['tax_id'],
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function update(Supplier $supplier, array $data): Supplier
    {
        // tax_id is immutable; drop it even if the payload includes it.
        unset($data['tax_id']);

        $supplier->fill([
            'company_name' => $data['company_name'] ?? $supplier->company_name,
            'phone' => $data['phone'] ?? $supplier->phone,
            'email' => $data['email'] ?? $supplier->email,
            'address' => $data['address'] ?? $supplier->address,
        ]);

        $supplier->save();

        return $supplier->refresh();
    }

    public function toggleActive(Supplier $supplier): Supplier
    {
        $supplier->is_active = ! $supplier->is_active;
        $supplier->save();

        return $supplier->refresh();
    }

    public function delete(Supplier $supplier): void
    {
        if ($this->hasDependencies($supplier)) {
            throw new RuntimeException(
                'No se puede eliminar el proveedor porque tiene medicamentos o pedidos asociados.'
            );
        }

        $supplier->delete();
    }

    public function hasDependencies(Supplier $supplier): bool
    {
        return $supplier->medications()->exists()
            || $supplier->purchaseOrders()->exists();
    }
}
