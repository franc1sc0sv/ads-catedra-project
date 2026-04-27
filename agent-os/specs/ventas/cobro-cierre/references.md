# References — Cobro y Cierre

## Codebase

- `app/Http/Controllers/Web/Auth/AuthController.php` — thin-controller pattern: validated input → service call → `View|RedirectResponse`. The `cobrar` and `comprobante` controller methods follow the same shape.

## Product

- **MVP Section 6: Ventas (POS)** — this feature is the closing step where money, inventario y registro must align atomically or not happen at all. Upstream: carrito armado, recetas validadas. Downstream: comprobante imprimible.

## Patterns referenced from CLAUDE.md

- `app/Enums/UserRole.php` — pattern for backed enums with `label()` helper, to mirror in `app/Enums/MetodoPago.php`.
- `App\Services\Auth\Contracts\AuthServiceInterface` — pattern for service+interface pairing, to mirror in `App\Services\Ventas\Contracts\VentaServiceInterface`.
- `App\Http\Middleware\EnsureRole` — applied via `role:salesperson` on the cobro/comprobante routes.

## Database

- `RegistroVentas` — table altered with `eMetodoPago` and `nMontoRecibido`.
- `RegistroDetalleVentas` — read per line during cierre to drive stock decrement.
- `RegistroProductos` (or equivalent) — locked + decremented per line.
- `RegistroMovimientosInventario` — one `SALIDA_VENTA` row inserted per línea, with `usuario_responsable_id`.

## Concurrency

- Laravel `lockForUpdate()` — pessimistic row lock used on both `Venta` (idempotency serialization) and `Producto` (stock race-condition guard).
- `DB::transaction` — automatic rollback on exception; domain exception `StockInsuficienteException` triggers rollback and is caught at the controller boundary.
