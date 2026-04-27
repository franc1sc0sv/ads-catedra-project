# Standards: Logout

## authentication/session-auth

Logout flow: `Auth::logout()` + `session()->invalidate()` + `session()->regenerateToken()`.

Route definition:
```php
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
```

Controllers return `View|RedirectResponse` — no `JsonResponse`.

## authentication/role-middleware

```php
Route::middleware(['auth', 'role:rolename'])->group(...);
```

The logout route uses `middleware('auth')` only — no role restriction. Any authenticated user regardless of role can log out.

## backend/php-architecture

Request lifecycle: Route → Controller → Service → Response.

Web controller path: `app/Http/Controllers/Web/[Domain]/[Name]Controller.php`

PHP 8.x patterns:
- Readonly constructor promotion for injected dependencies.
- `match` over `switch`.
- `declare(strict_types=1)` at the top of every PHP file.

## backend/service-interface

Controllers inject the interface, never the concrete class. Business logic belongs in the service.

Directory structure:
```
app/Services/Auth/
  AuthService.php
  Contracts/
    AuthServiceInterface.php
```

Bindings live in `AppServiceProvider`. The controller receives `AuthServiceInterface` via constructor injection.

## frontend/role-namespacing

The logout button appears in each role's nav component:

```
resources/views/components/nav/admin-nav.blade.php
resources/views/components/nav/salesperson-nav.blade.php
resources/views/components/nav/inventory-manager-nav.blade.php
resources/views/components/nav/pharmacist-nav.blade.php
```

Button pattern (POST form with CSRF):
```html
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Cerrar sesión</button>
</form>
```
