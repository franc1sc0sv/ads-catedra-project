# References — Reporte de Inventario

## Codebase

- **`app/Http/Controllers/Web/Auth/AuthController.php`** — Patrón de controller delgado: constructor con readonly promotion inyectando la interface, métodos retornan `View|RedirectResponse`, lógica delegada al service. Replicar en `Web/Reportes/ReporteInventarioController`.

## Convenciones a heredar

- `declare(strict_types=1)` al tope de cada PHP file.
- Servicio + `Contracts/` interface; binding en `AppServiceProvider`.
- Roles vía `App\Enums\UserRole` y middleware `role:administrator,inventory_manager` — nunca chequear rol dentro del controller.
- Vistas en `resources/views/admin/reportes/` y `resources/views/inventory-manager/reportes/` con partial compartido en `components/reportes/`.

## Producto

- **MVP Sección 8: Reportes y Auditoría** — este spec implementa el primer reporte de la sección. Próximos reportes (ventas, vencimientos, auditoría) reutilizarán el mismo patrón service + filtros GET + export CSV.
