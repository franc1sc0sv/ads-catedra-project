<?php

declare(strict_types=1);

namespace App\Http\Requests\Proveedores;

use Illuminate\Foundation\Http\FormRequest;

final class StoreProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'tax_id' => ['required', 'string', 'max:64', 'unique:suppliers,tax_id'],
            'phone' => ['nullable', 'string', 'max:64'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'company_name' => 'razón social',
            'tax_id' => 'identificador fiscal',
            'phone' => 'teléfono',
            'email' => 'correo',
            'address' => 'dirección',
            'is_active' => 'estado activo',
        ];
    }
}
