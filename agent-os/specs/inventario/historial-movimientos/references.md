# References — Historial de Movimientos

## Codebase

- `app/Http/Controllers/Web/Auth/AuthController.php` — patrón de controlador delgado: inyección de interfaz por constructor readonly, validación, llamada al servicio, retorno `View|RedirectResponse`. Modelar `MovimientoController` con la misma forma.

## Producto

- MVP Sección 3: **Inventario — auditoría**. Esta feature es la cara visible de esa auditoría sobre la tabla de movimientos.

## Cross-cutting

- Sólo `inventory_manager` y `administrator` acceden.
- Web-only (sin API paralelo).
- `declare(strict_types=1)` y constructor readonly en service y controller.
- Acciones devuelven `View|RedirectResponse`.

## Standards aplicados

Ver `standards.md` en este folder:

- `authentication/role-middleware`
- `authentication/session-auth`
- `backend/php-architecture`
- `backend/service-interface`
- `frontend/role-namespacing`
