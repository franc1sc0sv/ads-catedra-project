# Standards: historial-compras

## authentication/role-middleware

Routes for this feature must be grouped under both `auth` and `role` middleware:

```php
Route::middleware(['auth', 'role:salesperson,administrator'])->group(function () {
    Route::get('/clientes/{cliente}/historial', [ClienteController::class, 'historial'])
        ->name('clientes.historial');
});
```

The `EnsureRole` middleware reads `auth()->user()->role->value`. A role change takes effect on the user's next request.

## backend/php-architecture

Strict layering: Route → Controller → ServiceInterface → Service → Model → Response.

- Web controller lives at `app/Http/Controllers/Web/Clientes/ClienteController.php`
- Controller does: validate/bind → call service → return `View|RedirectResponse`
- No business logic in the controller

File conventions:
- `declare(strict_types=1)` at the top of every PHP file
- Readonly constructor promotion for injected dependencies:
  ```php
  public function __construct(
      private readonly ClienteServiceInterface $clienteService,
  ) {}
  ```

## backend/service-interface

Every service has a `Contracts/` interface alongside it. Controllers inject the interface, not the concrete class. Binding lives in `AppServiceProvider`.

Interface location: `app/Services/Clientes/Contracts/ClienteServiceInterface.php`

Required method signature:

```php
public function getHistorial(Cliente $cliente, int $perPage = 15): LengthAwarePaginator;
```

Implementation location: `app/Services/Clientes/ClienteService.php`

Implementation pattern:

```php
public function getHistorial(Cliente $cliente, int $perPage = 15): LengthAwarePaginator
{
    return $cliente->ventas()
        ->orderByDesc('fecha')
        ->paginate($perPage);
}
```

## frontend/role-namespacing

Views are namespaced by role to match the project convention:

```
resources/views/
  salesperson/clientes/historial.blade.php
  admin/clientes/historial.blade.php
  components/nav/salesperson-nav.blade.php   ← existing, reuse
  components/nav/admin-nav.blade.php         ← existing, reuse
```

Both views extend `layouts/app.blade.php` and use their respective nav component.

## General PHP conventions

- `match` over `switch`
- Backed enums with `label()` helper
- `casts()` method (not `$casts` property) — Laravel 12 style
- No JWT, no API routes — web-only, session auth
