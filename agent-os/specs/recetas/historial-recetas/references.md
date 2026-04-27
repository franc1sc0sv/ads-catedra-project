# References — Historial de Recetas

## Codebase

- `app/Http/Controllers/Web/Auth/AuthController.php` — Patrón de controlador delgado: validar input, delegar al service, retornar `View|RedirectResponse`. Aplicar el mismo patrón en `RecetaController::historial`.

## Standards aplicados

- `authentication/role-middleware` — Restringir ruta con `role:pharmacist,administrator`.
- `authentication/session-auth` — Toda la ruta vive bajo middleware `auth` (sesión).
- `backend/php-architecture` — Route → Controller → ServiceInterface → Service → Model; `declare(strict_types=1)`, readonly constructor, `match`, `casts()`.
- `backend/service-interface` — `RecetaServiceInterface` define `getHistorial`; `RecetaService` lo implementa; binding en `AppServiceProvider`.
- `frontend/role-namespacing` — Vistas en `resources/views/pharmacist/recetas/historial.blade.php` y `resources/views/admin/recetas/historial.blade.php`.

## Producto

- Plan-product MVP, Sección 7 (Recetas) — esta feature es la cara de auditoría del módulo de recetas; depende de los flujos de validación y venta-receta vinculada para tener datos que mostrar.
