<?php

declare(strict_types=1);

namespace App\Http\Requests\Usuarios;

use Illuminate\Foundation\Http\FormRequest;

final class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Autorización ya cubierta por middleware role:administrator a nivel ruta.
        return true;
    }

    public function rules(): array
    {
        return [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
