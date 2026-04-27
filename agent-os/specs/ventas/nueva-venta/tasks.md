# Tasks — Nueva Venta

- [x] **Task 1: Save spec documentation** (done)
- [ ] **Task 2: `EstadoVenta` enum** — `app/Enums/EstadoVenta.php` (backed string enum: `EN_PROCESO`, `COMPLETADA`, `CANCELADA`; `label()` helper).
- [ ] **Task 3: `RegistroVentas` model + migration** — fields `cveVenta`, `eEstado`, `cveCliente` (nullable FK), `nSubtotal`, `nImpuesto`, `nTotal`, `eMetodoPago` (nullable enum), `cveUsuarioCajero`, `fCreado`. `casts()` method maps `eEstado` to `EstadoVenta`. Relations: `cliente()`, `cajero()`, `detalles()`.
- [ ] **Task 4: `DetalleVenta` model + migration** — fields `cveDetalle`, `cveVenta`, `cveMedicamento`, `nCantidad`, `nPrecioUnitario`. Migration declares unique index on `(cveVenta, cveMedicamento)`. Relations: `venta()`, `medicamento()`.
- [ ] **Task 5: `VentaServiceInterface` + `VentaService`** — `app/Services/Ventas/Contracts/VentaServiceInterface.php` and `app/Services/Ventas/VentaService.php`. Methods:
  - `open(User $cajero): RegistroVentas|null` — return current `EN_PROCESO` venta of cajero, or null.
  - `addLine(RegistroVentas|null $venta, Medicamento $med, int $cantidad, User $cajero): RegistroVentas` — create venta if null, increment line if exists else insert, freeze price, verify stock, recalc.
  - `updateLine(DetalleVenta $detalle, int $cantidad): RegistroVentas` — change qty, verify stock, recalc.
  - `removeLine(DetalleVenta $detalle): RegistroVentas` — delete, recalc.
  - `attachClient(RegistroVentas $venta, Cliente $cliente): RegistroVentas`.
  - `recalculateTotal(RegistroVentas $venta): RegistroVentas` — internal.
  Bind interface to concrete in `AppServiceProvider`.
- [ ] **Task 6: FormRequests** — `app/Http/Requests/Ventas/AddLineaRequest.php`, `UpdateLineaRequest.php`, `AttachClienteRequest.php`. Validate `cveMedicamento`, `nCantidad>=1`, `cveCliente` exists.
- [ ] **Task 7: `VentaController`** — `app/Http/Controllers/Web/Ventas/VentaController.php`. Actions: `open` (GET, render POS with current venta), `addLine` (POST), `updateLine` (PATCH), `removeLine` (DELETE), `attachClient` (POST). Each returns `View|RedirectResponse`. Inject `VentaServiceInterface` via readonly constructor promotion.
- [ ] **Task 8: Routes** — `routes/web.php` group under `middleware(['auth', 'role:salesperson'])`, prefix `/ventas`. Map: `GET /ventas/pos` → `open`, `POST /ventas/lineas` → `addLine`, `PATCH /ventas/lineas/{detalle}` → `updateLine`, `DELETE /ventas/lineas/{detalle}` → `removeLine`, `POST /ventas/cliente` → `attachClient`.
- [ ] **Task 9: POS Blade view** — `resources/views/salesperson/ventas/pos.blade.php`. Layout: left panel = product search/lookup; right panel = cart (líneas con cantidad editable, botón remover) + totales en vivo (subtotal, impuesto, total) + botón "Asociar cliente". Uses `layouts/app.blade.php` and shared `components/ui/`.
