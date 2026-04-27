# References — Validar o Rechazar Receta

## Codebase

- `app/Http/Controllers/Web/Auth/AuthController.php` — patrón de thin controller a seguir: validated input → service → `View|RedirectResponse`. Sin lógica de negocio en el controller.

## Project Context

- **MVP Section 7: Recetas** — corazón del control regulatorio del producto. Esta feature implementa la decisión farmacéutica que destraba o detiene la venta de productos controlados.

## Cross-cutting

- `declare(strict_types=1)` en todos los archivos PHP nuevos.
- Readonly constructor promotion para inyección de dependencias en `RecetaService`.
- Solo rutas web (`routes/web.php`); no hay stack API paralelo.
- Restringido a rol `pharmacist` vía `EnsureRole` middleware.

## Related Specs

- Feature previa: carga inicial de receta (crea la receta en estado `PENDIENTE`, ya provee `cveRevisorActual` y `fLockExpira`).
- Feature siguiente: cajero quita producto controlado para destrabar venta cuando hay rechazo.
