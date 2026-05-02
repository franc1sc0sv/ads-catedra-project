<?php

declare(strict_types=1);

namespace App\Services\Clientes;

use App\Exceptions\Clientes\DuplicateIdentificationException;
use App\Exceptions\Clientes\InactiveCustomerException;
use App\Models\Customer;
use App\Services\Clientes\Contracts\CustomerServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CustomerService implements CustomerServiceInterface
{
    public function list(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Customer::query();

        if (($filters['incluir_inactivos'] ?? null) !== '1') {
            $query->where('is_active', true);
        }

        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $like = '%'.$search.'%';
            $query->where(function ($q) use ($like): void {
                $q->where('name', 'like', $like)
                    ->orWhere('identification', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone', 'like', $like);
            });
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function search(string $query): Collection
    {
        $query = trim($query);

        $builder = Customer::query()->where('is_active', true);

        if ($query !== '') {
            $builder->where(function ($q) use ($query): void {
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
        $existing = Customer::where('identification', $data['identification'])->first();

        if ($existing !== null) {
            if (! $existing->is_active) {
                throw new InactiveCustomerException($existing);
            }
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

    public function softDelete(Customer $customer): void
    {
        $customer->update(['is_active' => false]);
    }

    public function reactivate(Customer $customer): void
    {
        $customer->update(['is_active' => true]);
    }

    public function toggleFrecuente(Customer $customer): Customer
    {
        $customer->is_frequent = ! $customer->is_frequent;
        $customer->save();

        return $customer;
    }
}
