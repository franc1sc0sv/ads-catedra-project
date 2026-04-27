# References — Reporte de Ventas

## Codebase

- `app/Http/Controllers/Web/Auth/AuthController.php` — thin controller pattern: validated input → service call → `View|RedirectResponse`. Mirror this shape in `ReporteVentasController` (no business logic, no role checks inside the controller).

## Product

- MVP Section 8: Reportes y Auditoría — defines the administrator-only reporting surface. This spec is the first concrete report under that section.

## Conventions

- `App\Enums\UserRole::administrator` — only role allowed to access the route. Enforced by `role:administrator` middleware, not in-controller.
- Services live under `app/Services/<Domain>/` with sibling `Contracts/` interface; bound in `AppServiceProvider`. New service goes under `app/Services/Reportes/`.
- Views under `resources/views/admin/reportes/` per role-namespacing standard.
- All PHP files use `declare(strict_types=1)` and readonly constructor promotion for injected dependencies.

## External

- Laravel `streamDownload` for chunked CSV export — avoids loading full result set into memory.
