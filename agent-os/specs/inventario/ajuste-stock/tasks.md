# Tasks — Ajuste de Stock

- [x] **Task 1:** Save spec documentation (spec.md, shape.md, standards.md, references.md).

---

- [x] **Task 2:** Create `MovimientoInventario` model and migration.
  - File: `app/Models/MovimientoInventario.php`
  - File: `database/migrations/<timestamp>_create_movimientos_inventario_table.php`
  - Columns: `id`, `esTipo` (enum: `ajuste_manual`, `baja_vencimiento`, `devolucion`), `nCantidad` (signed integer, non-zero enforced at app layer), `nStockAntes` (integer), `nStockDespues` (integer), `cMotivo` (text, not null), `fMovimiento` (timestamp), `cveUsuario` (FK `users.id`), `cveMedicamento` (FK `medicamentos.id`), `created_at`, `updated_at`.
  - No `softDeletes`. No update path. Append-only.
  - Model: `declare(strict_types=1)`, `casts()` method (Laravel 12), backed enum cast for `esTipo`.
  - Relations: `belongsTo(User::class, 'cveUsuario')`, `belongsTo(Medicamento::class, 'cveMedicamento')`.

---

- [x] **Task 3:** Create the `TipoMovimientoInventario` backed enum.
  - File: `app/Enums/TipoMovimientoInventario.php`
  - Values: `ajuste_manual`, `baja_vencimiento`, `devolucion`.
  - `label(): string` helper (used in views).

---

- [x] **Task 4:** Create service interface and implementation.
  - File: `app/Services/Inventario/Contracts/StockServiceInterface.php`
  - File: `app/Services/Inventario/StockService.php`
  - Method: `ajustar(Medicamento $medicamento, array $data): MovimientoInventario`
  - Wrap logic in `DB::transaction(...)`:
    1. Read `nStockAntes` from `$medicamento->stock` inside the transaction.
    2. Compute `nStockDespues = nStockAntes + cantidad`.
    3. Guard: throw `\DomainException` if `nStockDespues < 0`.
    4. Update `$medicamento->stock` and save.
    5. Create and return `MovimientoInventario`.
  - Readonly constructor, `declare(strict_types=1)`, `match` over `switch`.

---

- [x] **Task 5:** Bind interface in `AppServiceProvider`.
  - File: `app/Providers/AppServiceProvider.php`
  - `$this->app->bind(StockServiceInterface::class, StockService::class);`

---

- [x] **Task 6:** Create `AjusteStockRequest` form request.
  - File: `app/Http/Requests/Inventario/AjusteStockRequest.php`
  - Rules:
    - `cve_medicamento`: `required|exists:medicamentos,id`
    - `tipo`: `required|in:ajuste_manual,baja_vencimiento,devolucion`
    - `cantidad`: `required|integer|not_in:0`
    - `motivo`: `required|string|min:5`
  - `authorize()` returns true (role enforcement is at the route layer).

---

- [x] **Task 7:** Create controller.
  - File: `app/Http/Controllers/Web/Inventario/AjusteStockController.php`
  - Readonly constructor injecting `StockServiceInterface`.
  - `create(): View` — form view with medicamento search + form.
  - `store(AjusteStockRequest $request): RedirectResponse` — resolve medicamento, call service, flash success, redirect; catch `\DomainException` and redirect back with error.
  - Thin: only validated input → service call → response.

---

- [x] **Task 8:** Register routes.
  - File: `routes/web.php`
  - Group `middleware(['auth', 'role:inventory_manager'])`.
  - `GET  /inventario/ajuste-stock` → `AjusteStockController@create` name `inventario.ajuste-stock.create`.
  - `POST /inventario/ajuste-stock` → `AjusteStockController@store`  name `inventario.ajuste-stock.store`.

---

- [x] **Task 9:** Build the view.
  - File: `resources/views/inventory-manager/inventario/ajuste.blade.php`
  - Uses `layouts/app.blade.php`, `x-nav.inventory-manager-nav`, `x-ui.*` shared components.
  - Form fields:
    - Medicamento selector (search/select `cve_medicamento`).
    - Dropdown for `tipo` (three options with `label()` from enum).
    - Integer input for `cantidad` (with note: negative = remove stock).
    - Textarea for `motivo` (required, min 5 chars hint).
    - Submit button.
  - Show server-side validation errors per field. Show flash success/error after store.

---

- [x] **Task 10:** Manual verification.
  - Login as `inventory@pharma.test`.
  - Apply each tipo (`ajuste_manual`, `baja_vencimiento`, `devolucion`) with positive and negative cantidades.
  - Confirm stock updates and `MovimientoInventario` row exists with correct `nStockAntes`/`nStockDespues`.
  - Confirm role enforcement: other roles get 403.
  - Confirm validation: empty motivo, cantidad=0, stock resultante negativo all rejected.
