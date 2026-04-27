# Standards: busqueda-venta

## authentication/role-middleware

```php
Route::middleware(['auth', 'role:salesperson,administrator'])->group(...)
```

Roles available in `App\Enums\UserRole`: `administrator`, `salesperson`, `inventory_manager`, `pharmacist`.

`EnsureRole` middleware reads `auth()->user()->role->value`. Role change takes effect on the user's next request.

---

## backend/php-architecture

Full request lifecycle:

```
Route → FormRequest → Controller → ServiceInterface → Service → Model → Response
```

File locations for this feature:

| Layer | Path |
|---|---|
| Controller | `app/Http/Controllers/Web/Clientes/ClienteBusquedaController.php` |
| Form Request | `app/Http/Requests/Clientes/QuickCreateClienteRequest.php` |
| Service Interface | `app/Services/Clientes/Contracts/ClienteServiceInterface.php` |
| Service | `app/Services/Clientes/ClienteService.php` |
| Model | `app/Models/Cliente.php` |

All PHP files use `declare(strict_types=1)`. Readonly constructor promotion for injected dependencies. `match` over `switch`.

---

## backend/service-interface

Controller injects the interface, never the concrete class:

```php
public function __construct(
    private readonly ClienteServiceInterface $clienteService,
) {}
```

Binding registered in `AppServiceProvider::register()`:

```php
$this->app->bind(ClienteServiceInterface::class, ClienteService::class);
```

---

## frontend/role-namespacing

Views namespaced by role:

```
resources/views/
  salesperson/ventas/          ← search component embedded here
  components/clientes/         ← busqueda.blade.php
  components/ui/               ← shared modal, input, button
```

Use `x-ui.*` shared components for the modal container and form inputs. Do not create role-specific UI primitives.
