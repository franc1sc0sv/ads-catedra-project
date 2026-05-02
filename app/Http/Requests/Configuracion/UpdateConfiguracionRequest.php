<?php

declare(strict_types=1);

namespace App\Http\Requests\Configuracion;

use App\Enums\SettingType;
use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateConfiguracionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $setting = Setting::where('key', $this->route('key'))->first();

        return match ($setting?->data_type) {
            SettingType::INTEGER => ['value' => ['required', 'integer', 'min:0']],
            SettingType::DECIMAL => ['value' => ['required', 'numeric', 'min:0']],
            SettingType::BOOLEAN => ['value' => ['required', 'in:0,1']],
            default              => ['value' => ['required', 'string', 'max:255']],
        };
    }

    public function messages(): array
    {
        return [
            'value.integer' => 'El valor debe ser un número entero.',
            'value.numeric' => 'El valor debe ser un número.',
            'value.min'     => 'El valor no puede ser negativo.',
            'value.in'      => 'El valor debe ser 0 o 1.',
        ];
    }
}
