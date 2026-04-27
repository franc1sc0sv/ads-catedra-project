<?php

declare(strict_types=1);

namespace App\Services\Auth\Contracts;

use App\Models\User;
use Illuminate\Http\Request;

interface AuthServiceInterface
{
    public function attemptLogin(string $email, string $password, bool $remember = false): bool;

    public function isCredentialsValidButInactive(string $email, string $password): bool;

    public function redirectPathAfterLogin(User $user): string;

    public function logout(Request $request): void;
}
