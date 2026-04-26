<?php
declare(strict_types=1);

namespace App\Services\Auth\Contracts;

use App\Models\User;

interface AuthServiceInterface
{
    public function updateProfile(User $user, array $data): User;
    public function redirectPathAfterLogin(User $user): string;
}
