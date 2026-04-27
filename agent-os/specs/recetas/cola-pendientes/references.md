# References: Cola de Pendientes

## Codebase

- `app/Http/Controllers/Web/Auth/AuthController.php` — thin controller pattern to mirror: readonly constructor injecting a service interface, action methods returning `View|RedirectResponse`, no business logic in the controller body.

## Project context

- `CLAUDE.md` (root) — confirms the controller namespacing (`app/Http/Controllers/Web/...`), service-interface pattern with `Contracts/` siblings binding in `AppServiceProvider`, role middleware usage, view namespacing per role under `resources/views/<role>/...`, and the `UserRole` enum that includes `pharmacist`.
- MVP Section 7: Recetas — establishes this view as the pharmacist's primary work surface.

## DBML / data model

- `agent-os/projects/.../project_ads404_pharmacy.md` (memory) — pharmacy schema with the `recetas`, `ventas`, `medicamentos`, `pacientes`, `medicos`, and `users` tables that this spec touches.

## Conventions

- `declare(strict_types=1)` at the top of every PHP file authored.
- Readonly constructor promotion for injected dependencies.
- `casts()` method on Eloquent models (Laravel 12), not the `$casts` property.
- Backed enums with a `label()` helper.
