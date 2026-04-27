<?php

declare(strict_types=1);

namespace App\Http\Requests\Usuarios;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::ADMINISTRATOR;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string', Rule::enum(UserRole::class)],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
