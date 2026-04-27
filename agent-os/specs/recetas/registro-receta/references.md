# References — Registro de Receta

## Codebase

- `app/Http/Controllers/Web/Auth/AuthController.php` — patrón de controlador delgado: validated input → llamada al servicio → `View|RedirectResponse`. `RecetaController@store` debe seguir esta misma forma (sin lógica de negocio en el controller).

## Producto

- **MVP Section 7: Recetas** — sin receta validada para cada medicamento controlado, no hay cobro. Esta feature cubre la captura (`PENDIENTE`); la validación posterior es feature separada.

## Cross-cutting constraints

- Service-interface obligatorio (`Contracts/RecetaServiceInterface`).
- Web-only — sin endpoints API paralelos.
- `declare(strict_types=1)` en todos los archivos PHP.
- Constructor promotion `readonly` para dependencias inyectadas.
- Controllers retornan `View|RedirectResponse`.
- Solo accesible para rol `salesperson` (`role:salesperson` middleware).
