<?php
declare(strict_types=1);

namespace App\Services\Auth;

use App\Enums\UserRole;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Auth\Contracts\AuthServiceInterface;
use App\Services\Jwt\JwtService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

class AuthService implements AuthServiceInterface
{
    public function __construct(private readonly JwtService $jwtService) {}

    public function register(array $data): array
    {
        $user  = User::create($data);
        $token = $this->jwtService->encode($user->id, $user->role->value);

        return ['user' => new UserResource($user), 'token' => $token];
    }

    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw new AuthenticationException('Invalid credentials.');
        }

        $token = $this->jwtService->encode($user->id, $user->role->value);

        return ['user' => new UserResource($user), 'token' => $token];
    }

    public function logout(string $token): void
    {
        $this->jwtService->blacklist($token);
    }

    public function refreshToken(User $user, string $oldToken): array
    {
        $this->jwtService->blacklist($oldToken);
        $token = $this->jwtService->encode($user->id, $user->role->value);

        return ['token' => $token];
    }

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
