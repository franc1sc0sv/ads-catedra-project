# Standards: Catálogo de Clientes

## authentication/role-middleware

Apply both `auth` and `role` middleware together. The `role` middleware accepts a comma-separated list of allowed role values.

```php
Route::middleware(['auth', 'role:salesperson,administrator'])->group(function () {
    Route::resource('clientes', ClienteController::class)->except(['show']);
    Route::patch('clientes/{cliente}/restore', [ClienteController::class, 'restore'])
        ->name('clientes.restore');
});
```

`EnsureRole` reads `auth()->user()->role->value` and aborts with 403 if the value is not in the allowed list. Role changes take effect on the user's next request.

## backend/php-architecture

Full request lifecycle:

```
Route → FormRequest → Controller → ServiceInterface → Service → Model → View|RedirectResponse
```

File locations for this feature:

| Artifact | Path |
|---|---|
| Controller | `app/Http/Controllers/Web/Clientes/ClienteController.php` |
| Service | `app/Services/Clientes/ClienteService.php` |
| Interface | `app/Services/Clientes/Contracts/ClienteServiceInterface.php` |
| Create request | `app/Http/Requests/Clientes/CreateClienteRequest.php` |
| Update request | `app/Http/Requests/Clientes/UpdateClienteRequest.php` |
| Model | `app/Models/Cliente.php` |

All files use `declare(strict_types=1)`. Constructor dependencies use readonly promotion. `match` preferred over `switch`. `casts()` method (not `$casts` property) for Laravel 12.

## backend/service-interface

The controller injects `ClienteServiceInterface`, never `ClienteService` directly. The binding is registered in `AppServiceProvider`:

```php
$this->app->bind(ClienteServiceInterface::class, ClienteService::class);
```

The interface defines the public contract:

```php
interface ClienteServiceInterface
{
    public function list(array $filters): LengthAwarePaginator;
    public function create(array $data): Cliente;
    public function update(Cliente $cliente, array $data): Cliente;
    public function deactivate(Cliente $cliente): void;
    public function restore(Cliente $cliente): void;
}
```

Soft-delete logic (bActivo flag toggle), identification lock check, and search filtering all live in the service — never in the controller.

## frontend/role-namespacing

Views are namespaced by role. Both roles use the same controller; the controller resolves the view path at runtime:

```php
private function resolveView(string $view): string
{
    return match (auth()->user()->role->value) {
        'administrator' => "admin.clientes.{$view}",
        'salesperson'   => "salesperson.clientes.{$view}",
    };
}
```

View file tree:

```
resources/views/
  admin/clientes/
    index.blade.php
    create.blade.php
    edit.blade.php
  salesperson/clientes/
    index.blade.php
    create.blade.php
    edit.blade.php
```

Shared UI components live in `resources/views/components/ui/` and are referenced as `<x-ui.button>`, `<x-ui.card>`, `<x-ui.input>`.
