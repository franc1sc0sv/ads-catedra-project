# References — Alertas de Stock

## Codebase

- `app/Http/Controllers/Web/Auth/AuthController.php` — Reference for the thin-controller pattern: validated input → service call → `View|RedirectResponse`. The new `AlertasStockController` should mirror this shape (single action, readonly constructor injection, no business logic in the controller body).
- `app/Services/Auth/Contracts/AuthServiceInterface.php` — Canonical example of the service-interface pattern. `AlertasStockServiceInterface` lives at `app/Services/Inventario/Contracts/` and is bound in `AppServiceProvider` to the concrete `AlertasStockService`.
- `app/Providers/AppServiceProvider.php` — Where the new `AlertasStockServiceInterface` → `AlertasStockService` binding must be registered alongside existing service bindings.
- `app/Enums/UserRole.php` — Source of truth for role values. Route middleware uses `role:inventory_manager,administrator`; the controller may dispatch view paths via `match` on `auth()->user()->role`.
- `routes/web.php` — Existing `auth` + `role:` middleware groups. Add the alertas-stock route inside an `auth` + `role:inventory_manager,administrator` group.
- `resources/views/layouts/app.blade.php` — Base layout the two role-specific dashboards extend.

## Domain / Schema

- `CatConfiguracion` — Key-value config table. Reads `umbral_aviso_stock_bajo` (default 0) and `dias_alerta_vencimiento` (default 30) at query time via `ConfiguracionService`.
- `Medicamento` — Source of truth for `nStockActual`, `nStockMinimo`, `fVencimiento`. Both queries live in `AlertasStockService`.

## Cross-spec links

- `inventario/crear-pedido` — Target of the action link from Block 1 rows. The crear-pedido form should accept a `medicamento_id` query param to prefill the line item.
- `inventario/ajuste-stock` — Target of the action link from Block 2 rows. The baja form should accept a `medicamento_id` query param and default the motivo to "vencimiento".

## Standards

See `standards.md` for the five matched standards (role-middleware, session-auth, php-architecture, service-interface, role-namespacing).
