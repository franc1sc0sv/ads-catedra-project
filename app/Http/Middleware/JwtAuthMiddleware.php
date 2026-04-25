<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\Jwt\JwtService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtAuthMiddleware
{
    public function __construct(private readonly JwtService $jwtService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        try {
            $payload = $this->jwtService->decode($token);
        } catch (\Throwable) {
            return response()->json(['message' => 'Invalid or expired token.'], 401);
        }

        $user = User::find($payload->sub);

        if (! $user) {
            return response()->json(['message' => 'User not found.'], 401);
        }

        auth()->setUser($user);
        $request->attributes->set('jwt_payload', $payload);

        return $next($request);
    }
}
