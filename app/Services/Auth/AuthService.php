<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\Auth\Contracts\AuthServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

final class AuthService implements AuthServiceInterface
{
    public function attemptLogin(string $email, string $password, bool $remember = false): bool
    {
        $credentials = [
            'email' => $email,
            'password' => $password,
            'is_active' => true,
        ];

        return Auth::attempt($credentials, $remember);
    }

    public function isCredentialsValidButInactive(string $email, string $password): bool
    {
        $user = User::query()->where('email', $email)->first();

        if ($user === null) {
            return false;
        }

        if (! Hash::check($password, $user->password)) {
            return false;
        }

        return $user->is_active === false;
    }

    public function redirectPathAfterLogin(User $user): string
    {
        return match ($user->role) {
            UserRole::ADMINISTRATOR => route('admin.dashboard'),
            UserRole::SALESPERSON => route('salesperson.dashboard'),
            UserRole::INVENTORY_MANAGER => route('inventory-manager.dashboard'),
            UserRole::PHARMACIST => route('pharmacist.dashboard'),
        };
    }

    public function logout(Request $request): void
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
