<?php
declare(strict_types=1);

namespace App\Services\Auth\Contracts;

use App\Models\User;

interface AuthServiceInterface
{
    public function register(array $data): array;
    public function login(array $credentials): array;
    public function logout(string $token): void;
    public function refreshToken(User $user, string $oldToken): array;
    public function updateProfile(User $user, array $data): User;
    public function redirectPathAfterLogin(User $user): string;
}
