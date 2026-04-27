# References: middleware-roles

## app/Http/Middleware/

Placement for `EnsureRole.php`. This directory holds all custom Laravel middleware. The middleware must be registered as the `role` alias in `bootstrap/app.php` before it can be used on routes.

## app/Enums/UserRole.php

Backed string enum defining the four valid role values:
- `administrator`
- `salesperson`
- `inventory_manager`
- `pharmacist`

`EnsureRole` compares the authenticated user's `role->value` against this set. The enum must be complete and accurate — any role value not defined here will never pass the middleware check.

## routes/web.php

The file where all route groups are declared. Every protected route group must include both `auth` (Laravel's built-in session authentication guard) and `role:<value>` (the `EnsureRole` alias). Example pattern:

```php
Route::middleware(['auth', 'role:administrator'])->group(function () {
    // admin-only routes
});

Route::middleware(['auth', 'role:salesperson'])->group(function () {
    // salesperson-only routes
});

Route::middleware(['auth', 'role:inventory_manager'])->group(function () {
    // inventory manager routes
});

Route::middleware(['auth', 'role:pharmacist'])->group(function () {
    // pharmacist routes
});
```

## bootstrap/app.php

Laravel 12 application bootstrap file. Middleware aliases are registered here inside `withMiddleware()`. The `role` alias must map to `App\Http\Middleware\EnsureRole::class`.
