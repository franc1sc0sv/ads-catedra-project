<?php

declare(strict_types=1);

namespace App\Http\Requests\Inventario;

use App\Enums\MovementType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class AjusteStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $allowedTypes = [
            MovementType::MANUAL_ADJUST->value,
            MovementType::EXPIRY_WRITEOFF->value,
            MovementType::CUSTOMER_RETURN->value,
        ];

        return [
            'medication_id' => ['required', 'integer', Rule::exists('medications', 'id')],
            'type' => ['required', 'string', Rule::in($allowedTypes)],
            'quantity' => ['required', 'integer', 'not_in:0'],
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.not_in' => 'La cantidad no puede ser cero.',
            'reason.min' => 'El motivo debe tener al menos 5 caracteres.',
        ];
    }
}
