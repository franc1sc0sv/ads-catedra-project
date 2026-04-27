<?php

declare(strict_types=1);

namespace App\Http\Requests\Proveedores;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // tax_id is immutable — no rule. The service drops it from payload anyway.
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'company_name' => 'razón social',
            'phone' => 'teléfono',
            'email' => 'correo',
            'address' => 'dirección',
        ];
    }
}
