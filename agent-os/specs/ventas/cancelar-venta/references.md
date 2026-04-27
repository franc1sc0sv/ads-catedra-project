# References: Cancelar Venta

## Codebase

- `app/Http/Controllers/Web/Auth/AuthController.php`
  Thin controller pattern: validated input → service call → return `View|RedirectResponse`. Replicate this shape in `VentaController.cancelEnProceso` and `VentaController.cancelCompletada`. No business logic in the controller.

## Cross-feature dependencies

- `App\Services\Bitacora\Contracts\BitacoraServiceInterface` — inject into `VentaService` to log `VENTA_CANCELADA` from both flows. The bitácora feature must expose a method that accepts an action enum and a context payload (motivo, venta id, user id).

- `App\Services\Ajuste\AjusteStockService` (or equivalent) — relevant only as the contrast case: manual `MovimientoInventario` records there have `cveVenta = NULL`. Confirm the `MovimientoInventario` model accepts both populated and null `cveVenta` before implementing.

- `App\Models\Venta`, `App\Models\DetalleVenta`, `App\Models\MovimientoInventario`, `App\Models\Lote` — touched by `cancelCompletada`. Verify relationship definitions (`venta->detalles`, `detalle->lote`) are in place.

- `App\Enums\VentaEstado` — must contain `EN_PROCESO`, `COMPLETADA`, `CANCELADA`. Used in the guards inside both service methods (e.g. throw if state does not match expected).

- `App\Enums\MovimientoTipo` (or similar) — must include `DEVOLUCION`. Used when creating `MovimientoInventario` rows in `cancelCompletada`.

## Product context

MVP Section 6: Ventas (POS). Cancelación es la operación más expuesta a fraude interno, por eso la separación estricta de roles y la bitácora obligatoria. Documento fuente: `agent-os/plan-product/specs/06-ventas/`.
