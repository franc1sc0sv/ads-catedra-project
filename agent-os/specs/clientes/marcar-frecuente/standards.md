# Standards: Marcar Cliente Frecuente

## authentication/role-middleware

Routes must be wrapped in the combined auth + role middleware group. Both `salesperson` and `administrator` roles are permitted for this feature.

```php
Route::middleware(['auth', 'role:salesperson,administrator'])->group(function () {
    Route::patch('/clientes/{cliente}/frecuente', [ClienteController::class, 'toggleFrecuente'])
         ->name('clientes.toggleFrecuente');
});
```

`EnsureRole` reads `auth()->user()->role->value`. Role change takes effect on the next request.

---

## backend/php-architecture

Thin controller pattern. The controller resolves the bound `Cliente` model via route-model binding, delegates entirely to the service, and returns the JSON response. No business logic in the controller.

```
PATCH /clientes/{cliente}/frecuente → ClienteController@toggleFrecuente
Service method: toggleFrecuente(Cliente $cliente): Cliente
```

Controller signature:

```php
public function toggleFrecuente(Cliente $cliente): JsonResponse
{
    $cliente = $this->clienteService->toggleFrecuente($cliente);
    return response()->json(['frecuente' => $cliente->bFrecuente]);
}
```

---

## backend/service-interface

Every service has a `Contracts/` interface. Controllers inject the interface, never the concrete class. Binding lives in `AppServiceProvider`.

`ClienteServiceInterface` must include:

```php
public function toggleFrecuente(Cliente $cliente): Cliente;
```

Directory layout:

```
app/Services/Clientes/
  ClienteService.php
  Contracts/
    ClienteServiceInterface.php
```

---

## frontend/role-namespacing

Toggle button and badge appear in role-namespaced view paths:

```
resources/views/salesperson/clientes/index.blade.php
resources/views/admin/clientes/index.blade.php
```

Shared badge component lives in the `ui` namespace:

```
resources/views/components/ui/badge-frecuente.blade.php
```

Usage:

```blade
<x-ui.badge-frecuente :frecuente="$cliente->bFrecuente" />
```

---

## General PHP Conventions (from CLAUDE.md)

- `declare(strict_types=1)` at the top of every PHP file.
- Readonly constructor promotion for injected dependencies.
- `match` over `switch`.
- `casts()` method (not `$casts` property) — Laravel 12 style.
- Backed enums with a `label()` helper method.
