---
name: Dual Auth (JWT + Session)
description: Two parallel auth stacks — JWT for API routes, Laravel session for web routes — strictly separated
type: project
---

# Dual Auth: JWT (API) + Session (Web)

> Skills: `/laravel-specialist` for auth flows, `/php-pro` for JWT implementation details.

Two parallel stacks, strictly separated — never mixed.

- `routes/api.php` → `jwt.auth` middleware → `JwtAuthMiddleware`
- `routes/web.php` → `auth` middleware → Laravel session

**Why separate:** web needs stateful sessions for CSRF/redirect flows; API clients can't use cookies.

Both stacks share `AuthServiceInterface` — business logic is not duplicated.

```php
// API route (stateless)
Route::middleware('jwt.auth')->group(...);

// Web route (stateful)
Route::middleware(['auth', 'role:administrator'])->group(...);
```

- Never add `jwt.auth` to web routes or `auth` to api routes.
- Web controller returns `View|RedirectResponse`; API controller returns `JsonResponse`.
