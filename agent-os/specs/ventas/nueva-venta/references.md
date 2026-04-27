# References — Nueva Venta

## Codebase

- `app/Http/Controllers/Web/Auth/AuthController.php` — Thin-controller pattern: validated input via FormRequest → service call via injected interface → return `View|RedirectResponse`. `VentaController` debe seguir la misma forma (readonly constructor promotion del `VentaServiceInterface`, sin lógica de negocio en el controlador).

## Product context

- MVP Section 6: Ventas (POS) — sección de mayor uso diario; este spec entrega la apertura y construcción del carrito, base sobre la que se montará el cierre y método de pago en una feature posterior.

## Standards aplicados

Ver `standards.md`:

- `authentication/role-middleware` — restringe rutas de `/ventas/*` a `role:salesperson`.
- `authentication/session-auth` — autenticación por sesión Laravel; sin JWT.
- `backend/php-architecture` — flujo Route → FormRequest → Controller → ServiceInterface → Service → Model.
- `backend/service-interface` — `VentaService` con su `VentaServiceInterface` en `Contracts/`, binding en `AppServiceProvider`.
- `frontend/role-namespacing` — vistas POS bajo `resources/views/salesperson/ventas/`.

## Cross-cutting

- `declare(strict_types=1)` en todos los archivos PHP nuevos.
- Readonly constructor promotion para inyección de dependencias.
- `casts()` method (Laravel 12 style) en los modelos para mapear `eEstado` → `EstadoVenta` enum.
- `match` sobre `switch` en cualquier branching por enum.
