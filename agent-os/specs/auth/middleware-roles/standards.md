# Standards: middleware-roles

## authentication/role-middleware

Use `role:<value>` middleware to guard routes:

```php
Route::middleware(['auth', 'role:administrator'])->group(...);
Route::middleware(['auth', 'role:pharmacist'])->group(...);
```

- Reads `$request->user()?->role?->value` each request.
- Returns 403 if role does not match.
- Role change takes effect on next request.
- Multiple roles: `role:administrator,pharmacist`
- Never check role inside controller.

Roles: `administrator`, `salesperson`, `inventory_manager`, `pharmacist`

---

## authentication/session-auth

Session-based auth only. No JWT. No parallel API stack.

The role is read from the authenticated session user (`auth()->user()` / `$request->user()`). Laravel resolves the user from the session before middleware runs, so the role is always available when `EnsureRole` executes — provided `auth` precedes `role` in the middleware stack.

---

## backend/php-architecture

File placement:
- Middleware: `app/Http/Middleware/EnsureRole.php`
- Enum: `app/Enums/UserRole.php`

PHP 8.x conventions:
- `declare(strict_types=1)` in every PHP file.
- Backed enum (string) with a `label()` helper using `match` over `switch`.
- Readonly constructor promotion for injected dependencies (not applicable to this middleware — no constructor needed).

---

## backend/service-interface

Not directly applicable to `EnsureRole` (middleware is not a service and does not need an interface). However, the principle applies: keep the middleware thin. No business logic, no database queries, no service calls inside `EnsureRole`. It reads one value, compares it, and passes or aborts.

---

## frontend/role-namespacing

Views and nav components are namespaced by role:

```
resources/views/
  admin/dashboard/
  salesperson/dashboard/
  inventory-manager/dashboard/
  pharmacist/dashboard/
  components/nav/<role>-nav.blade.php
```

Role-namespaced views are only reachable via role-namespaced routes. `EnsureRole` is the enforcement layer that ensures a salesperson cannot reach `admin/dashboard/` routes and vice versa. Every role-specific route group must declare the correct `role:` middleware for this namespacing to be meaningful.
