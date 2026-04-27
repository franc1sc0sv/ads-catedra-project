<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if ($request->user() === null) {
            return redirect()->route('login');
        }

        $userRole = $request->user()->role?->value;

        if ($userRole === null || ! in_array($userRole, $roles, strict: true)) {
            abort(403);
        }

        return $next($request);
    }
}
