<?php

declare(strict_types=1);

namespace App\Services\Clientes\Contracts;

use App\Models\Customer;
use Illuminate\Support\Collection;

interface CustomerServiceInterface
{
    public function search(string $query): Collection;

    public function quickCreate(array $data): Customer;
}
