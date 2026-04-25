<?php
declare(strict_types=1);

namespace App\Services\Jwt;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class JwtService
{
    private string $secret;
    private int    $ttl;
    private string $algo;

    public function __construct()
    {
        $this->secret = config('jwt.secret');
        $this->ttl    = config('jwt.ttl');
        $this->algo   = config('jwt.algo');
    }

    public function encode(int $userId, string $role): string
    {
        $now = time();

        $payload = [
            'sub'  => $userId,
            'role' => $role,
            'jti'  => Str::uuid()->toString(),
            'iat'  => $now,
            'exp'  => $now + ($this->ttl * 60),
        ];

        return JWT::encode($payload, $this->secret, $this->algo);
    }

    public function decode(string $token): object
    {
        $decoded = JWT::decode($token, new Key($this->secret, $this->algo));

        if ($this->isBlacklisted($decoded->jti)) {
            throw new \RuntimeException('Token has been revoked.');
        }

        return $decoded;
    }

    public function blacklist(string $token): void
    {
        try {
            $decoded    = JWT::decode($token, new Key($this->secret, $this->algo));
            $remainingSec = $decoded->exp - time();

            if ($remainingSec > 0) {
                Cache::put("jwt_blacklist_{$decoded->jti}", true, $remainingSec);
            }
        } catch (\Throwable) {
            // Token already invalid — nothing to blacklist
        }
    }

    private function isBlacklisted(string $jti): bool
    {
        return Cache::has("jwt_blacklist_{$jti}");
    }
}
