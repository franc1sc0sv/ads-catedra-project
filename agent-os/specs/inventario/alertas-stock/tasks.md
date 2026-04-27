# Tasks: Alertas de Stock

## Task 1 — Docs (done)
- [x] spec.md written
- [x] tasks.md written
- [x] shape.md written
- [x] standards.md written
- [x] references.md written

---

## Task 2 — Service Interface

**File:** `app/Services/Inventario/Contracts/AlertasStockServiceInterface.php`

```php
<?php

declare(strict_types=1);

namespace App\Services\Inventario\Contracts;

use Illuminate\Support\Collection;

interface AlertasStockServiceInterface
{
    /** Products where nStockActual < (nStockMinimo + umbral_aviso_stock_bajo), ordered by urgency asc. */
    public function getBajoMinimo(): Collection;

    /** Products where fVencimiento is within dias_alerta_vencimiento days, ordered by fVencimiento asc. */
    public function getProximosVencer(): Collection;
}
```

---

## Task 3 — Service Implementation

**File:** `app/Services/Inventario/AlertasStockService.php`

- Inject `ConfiguracionServiceInterface` (readonly constructor).
- `getBajoMinimo()`: read `umbral_aviso_stock_bajo` (int, default 0) from config; query `Medicamento` where `nStockActual < nStockMinimo + $umbral`; order by `nStockActual / nStockMinimo` ascending (raw expression or `orderByRaw`); return Collection.
- `getProximosVencer()`: read `dias_alerta_vencimiento` (int, default 30) from config; query `Medicamento` where `fVencimiento` between `today` and `today + $dias days`; order by `fVencimiento` ascending; return Collection.
- Both queries eager-load only the columns needed for the view (select specific columns or leave as `*` if schema is narrow).

---

## Task 4 — AppServiceProvider Binding

In `app/Providers/AppServiceProvider.php`, register:

```php
$this->app->bind(AlertasStockServiceInterface::class, AlertasStockService::class);
```

---

## Task 5 — Controller

**File:** `app/Http/Controllers/Web/Inventario/AlertasStockController.php`

- Single `__invoke` method (or `index`).
- Inject `AlertasStockServiceInterface` via readonly constructor.
- Call both service methods, pass results to view.
- `inventory_manager` → `inventory-manager/inventario/alertas`
- `administrator` → `admin/inventario/alertas`
- Use `auth()->user()->role->value` to determine which view to return, or share a single view from a shared path.

---

## Task 6 — Routes

In `routes/web.php`, under the `auth` + `role:inventory_manager,administrator` group:

```php
Route::middleware(['auth', 'role:inventory_manager,administrator'])
    ->group(function () {
        Route::get('/inventario/alertas-stock', AlertasStockController::class)
            ->name('inventario.alertas-stock');
    });
```

---

## Task 7 — Views

**File:** `resources/views/inventory-manager/inventario/alertas.blade.php`

- Extends `layouts/app.blade.php`.
- Two card sections: "Bajo Mínimo" and "Próximos a Vencer".
- Each section renders a table: product name, stock columns, expiry (where applicable), action link.
- Empty-state message when a block has no rows.
- "Crear pedido" link per row in block 1 (route to be defined in crear-pedido spec).
- "Registrar baja" link per row in block 2 (route to ajuste-stock baja form).

**File:** `resources/views/admin/inventario/alertas.blade.php`

- Same layout, same two blocks.
- Action links present (they lead to role-protected routes the admin cannot use, but the view remains identical to avoid duplication; alternatively, hide with `@can` if policy is added later).

---

## Task 8 — Manual Smoke Test

1. Seed a product with `nStockActual < nStockMinimo` → appears in Block 1.
2. Seed a product with `fVencimiento` within 30 days → appears in Block 2.
3. Seed a product matching both conditions → appears in both blocks.
4. Log in as `inventory@pharma.test`, visit `/inventario/alertas-stock` → correct view loads.
5. Log in as `admin@pharma.test`, visit same URL → correct view loads.
6. Log in as `sales@pharma.test`, visit same URL → 403 / redirect.
