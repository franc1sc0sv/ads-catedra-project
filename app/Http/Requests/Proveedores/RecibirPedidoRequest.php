<?php

declare(strict_types=1);

namespace App\Http\Requests\Proveedores;

use Illuminate\Foundation\Http\FormRequest;

final class RecibirPedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.quantity_received' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'items' => 'líneas',
            'items.*.quantity_received' => 'cantidad recibida',
            'items.*.unit_price' => 'precio real',
        ];
    }
}
