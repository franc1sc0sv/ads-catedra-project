# Tasks: busqueda-venta

## Task 1 — Docs ✅
Review spec.md, shape.md, and standards.md. Confirm scope before writing any code.

---

## Task 2 — CustomerServiceInterface ✅

Create `app/Services/Clientes/Contracts/ClienteServiceInterface.php`.

```php
<?php

declare(strict_types=1);

namespace App\Services\Clientes\Contracts;

use App\Models\Cliente;
use Illuminate\Support\Collection;

interface ClienteServiceInterface
{
    public function search(string $query): Collection;
    public function quickCreate(array $data): Cliente;
}
```

Register binding in `AppServiceProvider`:
```php
$this->app->bind(ClienteServiceInterface::class, ClienteService::class);
```

---

## Task 3 — CustomerService ✅

Create `app/Services/Clientes/ClienteService.php`.

- `search(string $query): Collection` — LIKE query on `nombre` and `identificacion`, `activo = true`, limit 10, return `id`, `nombre`, `identificacion`, `frecuente`.
- `quickCreate(array $data): Cliente` — check for duplicate `identificacion` (any `activo` value) and throw a domain exception if found; otherwise create and return the record.

---

## Task 4 — CustomerSearchController ✅

Create `app/Http/Controllers/Web/Clientes/ClienteBusquedaController.php`.

Two actions:
- `search(Request $request)` — accepts `q` query param, delegates to `ClienteServiceInterface::search()`, returns `response()->json($results)`.
- `quickCreate(QuickCreateClienteRequest $request)` — delegates to `ClienteServiceInterface::quickCreate()`, returns JSON with the new client or a 422 with `identificacion_duplicada` error key.

Inject `ClienteServiceInterface` via readonly constructor promotion. No business logic in the controller.

---

## Task 5 — QuickCreateCustomerRequest ✅

Create `app/Http/Requests/Clientes/QuickCreateClienteRequest.php`.

Rules:
- `nombre`: required, string, max 255
- `telefono`: required, string, max 20
- `identificacion`: required, string, max 50

---

## Task 6 — Routes ✅

In `routes/web.php`, add under `['auth', 'role:salesperson,administrator']` middleware:

```php
Route::prefix('clientes')->name('clientes.')->group(function () {
    Route::get('/buscar', [ClienteBusquedaController::class, 'search'])->name('buscar');
    Route::post('/quick-create', [ClienteBusquedaController::class, 'quickCreate'])->name('quick-create');
});
```

---

## Task 7 — Blade Autocomplete Component ✅

> Implementation note: Spec used Spanish c-prefix names (`Cliente`, `nombre`, `identificacion`, `frecuente`, `activo`); the codebase's canonical English schema (`Customer`, `name`, `identification`, `is_frequent`, `is_active`) was used instead per the project's existing-code decisions. Embedding into `resources/views/salesperson/ventas/create.blade.php` is deferred — the ventas POS create-sale view doesn't exist yet (out-of-scope branch).


Create `resources/views/components/clientes/busqueda.blade.php` (x-clientes.busqueda).

- Text input that fires a `fetch` to `route('clientes.buscar')` on `input` (debounced ~300 ms via Alpine.js).
- Dropdown renders up to 10 results; each row shows `nombre` + `identificacion`; frecuente clients show a badge.
- "Crear cliente" option at the bottom when results are empty.
- Modal (x-ui.modal or inline Alpine dialog) with the three quick-create fields; submits to `route('clientes.quick-create')` via `fetch`; shows inline duplicate warning on 422.
- On client select or quick-create success, emits a custom browser event `cliente-seleccionado` with `{ id, nombre, frecuente }` so the parent venta view can bind the association without a page reload.

Embed the component in `resources/views/salesperson/ventas/create.blade.php`:
```blade
<x-clientes.busqueda />
```
