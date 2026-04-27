# References — Ajuste de Stock

## Codebase

- **`app/Http/Controllers/Web/Auth/AuthController.php`** — Thin controller pattern. Demonstrates readonly constructor injection of a `ServiceInterface`, validated-input → service-call → response flow, and `View|RedirectResponse` return types. `AjusteStockController` mirrors this shape exactly.

- **`app/Services/Auth/Contracts/AuthServiceInterface.php`** — Interface pattern under `Contracts/`. `StockServiceInterface` lives in `app/Services/Inventario/Contracts/` following the same convention.

- **`app/Services/Auth/AuthService.php`** — Concrete service pattern: business logic in service, not in controller. NOTE: contains JWT bits which are legacy — ignore them. The relevant pattern is the class shape, dependency injection, and method signatures.

## Architecture conventions

- **Role middleware.** `EnsureRole` reads `auth()->user()->role->value`. `role:inventory_manager` middleware key is already registered; no new middleware registration is required.
- **Controller namespacing.** Existing controllers live under `app/Http/Controllers/Web/{Domain}/`. This feature adds `app/Http/Controllers/Web/Inventario/` as a new domain subfolder, parallel to `Web/Auth/` and `Web/Dashboard/`.
- **View namespacing.** `resources/views/inventory-manager/` is the role-namespaced view root for inventory manager screens, parallel to `resources/views/admin/` and `resources/views/salesperson/`. The new view `ajuste.blade.php` lives under `resources/views/inventory-manager/inventario/`.

## Schema

- **`medicamentos` table.** Contains `stock` (integer) column that this feature updates. The `cveMedicamento` foreign key on `movimientos_inventario` points here.
- **`movimientos_inventario` table (to be created).** Defined in the ADS404 DBML schema as an append-only audit table. Columns align with the migration specified in `tasks.md`.
- **`users` table.** `cveUsuario` on `movimientos_inventario` references `users.id`. The authenticated user's ID is captured at write time via `auth()->id()`.

## Product

- **MVP Section 3 — Inventario.** Ajuste de Stock es la única vía oficial para corregir discrepancias fuera de los flujos de ventas y pedidos.

## Visuals

- None provided. UI follows the existing Blade + Tailwind v4 + Alpine.js conventions used in the auth reference module.
