# References — Listado de Pedidos

## Codebase

- `app/Http/Controllers/Web/Auth/AuthController.php` — thin controller pattern: validated input → service call → `View|RedirectResponse`. Same shape applies to `PedidoController`.

## Product context

- MVP Section 4: Proveedores y Pedidos. This spec covers the list/detail/state-transition surface; creation and reception are separate specs.

## Cross-cutting constraints

- `declare(strict_types=1)` on every PHP file.
- Readonly constructor promotion for injected dependencies.
- Web-only stack (session auth, no API/JWT).
- Controllers return `View|RedirectResponse`.
- Authorized roles for this feature: `inventory_manager` (operates), `administrator` (read-only).
