# References: Recibir Pedido

## Codebase

- `app/Http/Controllers/Web/Auth/AuthController.php` — patrón de controller delgado (constructor readonly, inyección de interface, retorno `View|RedirectResponse`). El `PedidoController::recibir` debe seguir esta misma forma.

## Product context

- **MVP Sección 4 — Proveedores y Pedidos.** La recepción es el evento que actualiza stock automáticamente; cualquier reporte de inventario depende de que `MovimientoInventario` quede correctamente atado al pedido origen.

## Cross-cutting constraints

- `declare(strict_types=1)` en cada archivo PHP nuevo.
- Constructor readonly con promoción de propiedades para dependencias inyectadas.
- Web-only: rutas en `routes/web.php`, sin contrapartes en `routes/api.php`.
- Solo `inventory_manager` puede ejecutar la recepción.
- Controllers retornan `View|RedirectResponse`.

## Standards aplicables

Ver `standards.md`:
- `authentication/role-middleware`
- `authentication/session-auth`
- `backend/php-architecture`
- `backend/service-interface`
- `frontend/role-namespacing`
