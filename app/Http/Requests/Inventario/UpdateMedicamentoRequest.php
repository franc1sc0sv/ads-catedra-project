<?php

declare(strict_types=1);

namespace App\Http\Requests\Inventario;

use App\Enums\MedicationCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

final class UpdateMedicamentoRequest extends FormRequest
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
        $medicamento = $this->route('medicamento');
        $id = is_object($medicamento) ? $medicamento->id : $medicamento;

        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'barcode' => [
                'required',
                'string',
                'max:64',
                Rule::unique('medications', 'barcode')->ignore($id),
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'expires_at' => ['required', 'date'],
            'category' => ['required', new Enum(MedicationCategory::class)],
            'supplier_id' => ['required', 'integer', Rule::exists('suppliers', 'id')],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
