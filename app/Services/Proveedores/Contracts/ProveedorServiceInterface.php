<?php

declare(strict_types=1);

namespace App\Services\Proveedores\Contracts;

use App\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProveedorServiceInterface
{
    public function list(?string $search = null, int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): Supplier;

    public function update(Supplier $supplier, array $data): Supplier;

    public function toggleActive(Supplier $supplier): Supplier;

    public function delete(Supplier $supplier): void;

    public function hasDependencies(Supplier $supplier): bool;
}
