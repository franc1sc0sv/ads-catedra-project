<?php

declare(strict_types=1);

namespace App\Services\Clientes\Contracts;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CustomerServiceInterface
{
    public function list(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function search(string $query): Collection;

    public function quickCreate(array $data): Customer;

    public function softDelete(Customer $customer): void;

    public function reactivate(Customer $customer): void;

    public function toggleFrecuente(Customer $customer): Customer;
}
