<?php
declare(strict_types=1);

namespace App\Services\Auth;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\Auth\Contracts\AuthServiceInterface;
use Illuminate\Support\Facades\Hash;

class AuthService implements AuthServiceInterface
{
    public function updateProfile(User $user, array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return $user->fresh();
    }

    public function redirectPathAfterLogin(User $user): string
    {
        return match($user->role) {
            UserRole::ADMINISTRATOR     => route('admin.dashboard'),
            UserRole::SALESPERSON       => route('sales.dashboard'),
            UserRole::INVENTORY_MANAGER => route('inventory.dashboard'),
            UserRole::PHARMACIST        => route('pharmacy.dashboard'),
        };
    }
}
