# Tasks: middleware-roles

## Task 1 — Read and confirm spec (docs)
- [x] Read `agent-os/specs/auth/middleware-roles/spec.md` and confirm scope is understood.
- [x] No code changes in this task.

## Task 2 — Create or verify `EnsureRole` middleware
- [x] Check if `app/Http/Middleware/EnsureRole.php` exists.
- [x] If missing, create it with `declare(strict_types=1)`.
- [x] Middleware must read `$request->user()?->role?->value` on every request.
- [x] Parse the `$role` parameter as a comma-separated list of allowed values.
- [x] If the user's role value is in the allowed list, call `$next($request)`.
- [x] If not, call `abort(403)`.
- [x] No business logic, no caching, no redirects — only the check and pass/abort.

```php
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
        $userRole = $request->user()?->role?->value;

        if ($userRole === null || !in_array($userRole, $roles, strict: true)) {
            abort(403);
        }

        return $next($request);
    }
}
```

## Task 3 — Register `role` alias in `bootstrap/app.php`
- [x] Open `bootstrap/app.php`.
- [x] Register `EnsureRole` as the `role` middleware alias inside `withMiddleware()`.
- [x] Confirm the alias name is exactly `role` so routes can use `role:administrator`, etc.

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\EnsureRole::class,
    ]);
})
```

## Task 4 — Verify `app/Enums/UserRole.php` has all four roles
- [x] Open `app/Enums/UserRole.php`.
- [x] Confirm it is a backed enum (string) with cases: `administrator`, `salesperson`, `inventory_manager`, `pharmacist`.
- [x] Confirm it has a `label()` helper method using `match`.
- [x] If any case or method is missing, add it.

## Task 5 — Apply `role` middleware to all route groups in `routes/web.php`
- [x] Open `routes/web.php`.
- [x] Confirm every role-protected route group includes both `auth` and `role:<value>` middleware.
- [x] Expected groups:
  - `['auth', 'role:administrator']` for admin routes
  - `['auth', 'role:salesperson']` for salesperson routes
  - `['auth', 'role:inventory_manager']` for inventory manager routes
  - `['auth', 'role:pharmacist']` for pharmacist routes
- [x] Do not check role inside any controller — remove any such checks if found.
