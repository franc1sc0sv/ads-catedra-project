# References — Catálogo de Medicamentos

## Codebase

- `app/Http/Controllers/Web/Auth/AuthController.php` — Reference for thin controller pattern: validated input → service call → `View|RedirectResponse`. Mirror this style in `MedicamentoController`.
- `app/Services/Auth/Contracts/AuthServiceInterface.php` — Reference for the `Contracts/` interface pattern. `MedicamentoServiceInterface` lives at `app/Services/Inventario/Contracts/` and is bound in `AppServiceProvider`.
- `app/Enums/UserRole.php` — Pattern for backed enum + `label()` helper. `CategoriaMedicamento` follows same shape.
- `app/Http/Middleware/EnsureRole.php` — Already supports comma-separated roles (`role:inventory_manager,salesperson,pharmacist`); use as-is for read routes.
- `routes/web.php` — Where read/write route groups for medicamentos register.
- `resources/views/layouts/app.blade.php` — Base layout for role dashboards; medicamento views extend it.

## Product Context

- Plan-product MVP Section 3 (Inventario) — Catálogo es la base sobre la que se montan movimientos, ventas y reportes. Sin catálogo, ningún otro flujo del módulo funciona.

## Cross-cutting

- Movimientos de inventario (sub-feature) — `MedicamentoService::crear()` crea un `Movimiento` tipo `ajuste_manual`. La tabla `movimientos` y su modelo se asumen ya presentes (o creados como prerequisito); si no existen aún, esta spec depende de su scaffolding mínimo.
- Ventas EN_PROCESO — guard de desactivación lee `ventas` con estado `EN_PROCESO`. Misma asunción que arriba.
