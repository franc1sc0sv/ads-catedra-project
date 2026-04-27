# References — Reporte de Movimientos

## Codebase

- `app/Http/Controllers/Web/Auth/AuthController.php` — Reference for the thin-controller pattern: validated input → service call → `View|RedirectResponse`. The new `ReporteMovimientosController` follows the same shape but returns `View|StreamedResponse` for the CSV export action.

## Product context

- MVP Section 8: Reportes y Auditoría — defines this report as part of the audit surface for inventory discrepancies.

## Related concepts (not yet implemented)

- `MovimientoInventario` model and `movimientos_inventario` table — source of truth for the listing.
- `Venta` and `Pedido` models — targets of the origin links per row.
- `User` model — used for the "usuario responsable" column and filter.
- `admin-nav.blade.php` — destination for the navigation entry once implemented.
