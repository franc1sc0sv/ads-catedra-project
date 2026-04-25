<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // For API requests: read role from the JWT payload (stateless — no extra DB query)
        $payload  = $request->attributes->get('jwt_payload');
        $userRole = $payload?->role ?? $request->user()?->role?->value;

        if (! $userRole || ! in_array($userRole, $roles, strict: true)) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
