<?php

declare(strict_types=1);

namespace App\Http\Requests\Inventario;

use App\Enums\MedicationCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

final class CreateMedicamentoRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'barcode' => ['required', 'string', 'max:64', Rule::unique('medications', 'barcode')],
            'price' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'stock_inicial' => ['nullable', 'integer', 'min:0'],
            'expires_at' => ['required', 'date', 'after:today'],
            'category' => ['required', new Enum(MedicationCategory::class)],
            'supplier_id' => ['required', 'integer', Rule::exists('suppliers', 'id')],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'expires_at.after' => 'La fecha de vencimiento debe ser posterior a hoy.',
            'price.min'        => 'El precio debe ser mayor a cero.',
            'price.max'        => 'El precio no puede superar $999,999.99.',
        ];
    }
}
