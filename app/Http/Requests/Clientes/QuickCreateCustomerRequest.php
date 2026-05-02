<?php

declare(strict_types=1);

namespace App\Http\Requests\Clientes;

use Illuminate\Foundation\Http\FormRequest;

class QuickCreateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:255'],
            'phone'          => ['required', 'string', 'regex:/^(\+?503[\s-]?)?[267]\d{3}[\s-]?\d{4}$/'],
            'identification' => ['required', 'string', 'regex:/^\d{8}-\d{1}$/', 'unique:customers,identification'],
        ];
    }

    public function messages(): array
    {
        return [
            'identification.regex'  => 'El formato del DUI debe ser ########-#',
            'identification.unique' => 'Ya existe un cliente con ese DUI.',
            'phone.regex'           => 'El teléfono debe iniciar con 2, 6 o 7 (####-####). Opcional: prefijo +503.',
        ];
    }
}
