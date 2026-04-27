# References — Catálogo de Proveedores

## Codebase

- `app/Http/Controllers/Web/Auth/AuthController.php` — patrón de controlador delgado: inyecta interfaz de servicio en constructor `readonly`, valida vía FormRequest, retorna `View|RedirectResponse`. Replicar en `ProveedorController`.
- `app/Services/Auth/Contracts/AuthServiceInterface.php` — patrón de interfaz: contrato con tipos estrictos en `Contracts/`, implementación en el directorio padre, binding en `AppServiceProvider`. Replicar para `ProveedorServiceInterface`.

## Product Context

- MVP Section 4: Proveedores y Pedidos — el catálogo de proveedores alimenta el módulo de pedidos. Esta spec cubre solo el catálogo; la integración con pedidos vive en su propia spec del dominio `pedidos`.

## Cross-cutting Constraints

- Service-interface obligatorio.
- Web-only (sin endpoints API ni JWT).
- `declare(strict_types=1)` en todos los archivos PHP.
- Constructor `readonly` con promoción de propiedades.
- Controladores retornan `View|RedirectResponse`.
- Acceso restringido a `inventory_manager` y `administrator` vía middleware `role:`.
