<?php

declare(strict_types=1);

namespace App\Services\Clientes;

use App\Exceptions\Clientes\DuplicateIdentificationException;
use App\Models\Customer;
use App\Services\Clientes\Contracts\CustomerServiceInterface;
use Illuminate\Support\Collection;

class CustomerService implements CustomerServiceInterface
{
    public function search(string $query): Collection
    {
        $query = trim($query);

        $builder = Customer::query()->where('is_active', true);

        if ($query !== '') {
            $builder->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('identification', 'like', "%{$query}%");
            });
        }

        return $builder
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'identification', 'is_frequent']);
    }

    public function quickCreate(array $data): Customer
    {
        $exists = Customer::where('identification', $data['identification'])->exists();

        if ($exists) {
            throw new DuplicateIdentificationException('La identificación ya está registrada.');
        }

        return Customer::create([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'identification' => $data['identification'],
            'is_active' => true,
            'is_frequent' => false,
        ]);
    }
}
