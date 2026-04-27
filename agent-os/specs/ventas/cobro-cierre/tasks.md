# Tasks — Cobro y Cierre

- [x] **Task 1**: Save spec documentation in `agent-os/specs/ventas/cobro-cierre/` (spec, shape, standards, references, tasks).
- [ ] **Task 2**: Create `app/Enums/MetodoPago.php` (backed enum: `efectivo`, `transferencia`, `debito`, `tarjeta`) with `label()` helper.
- [ ] **Task 3**: Add migration to alter `RegistroVentas` table — columns `eMetodoPago` (string, nullable until cierre) and `nMontoRecibido` (decimal, nullable).
- [ ] **Task 4**: Extend `App\Services\Ventas\Contracts\VentaServiceInterface` with `cobrar(Venta $venta, array $payment): Venta`.
- [ ] **Task 5**: Implement `VentaService::cobrar` — wrap in `DB::transaction`, lock venta with `lockForUpdate`, idempotency check on `EN_PROCESO`, lock+decrement stock per line, create `SALIDA_VENTA` movement per line with `usuario_responsable_id = auth()->id()`, mark venta `COMPLETADA`, persist `metodo_pago` and `monto_recibido`. Throw domain exception on stock conflict (caller handles rollback flash).
- [ ] **Task 6**: Create `App\Http\Requests\Ventas\CobroRequest` — rules: `metodo` in `MetodoPago` values, `monto_recibido` `required_if:metodo,efectivo` and `gte:total`.
- [ ] **Task 7**: Add `VentaController::cobrar(CobroRequest, Venta)` — call service, on success `redirect()->route('salesperson.ventas.comprobante', $venta)`, on stock conflict catch and `back()->withErrors(...)` keeping venta in `EN_PROCESO`.
- [ ] **Task 8**: Add `VentaController::comprobante(Venta)` — guard estado `COMPLETADA`, return `comprobante.blade.php`.
- [ ] **Task 9**: Register routes in `routes/web.php` under `auth` + `role:salesperson`: `POST /salesperson/ventas/{venta}/cobrar` and `GET /salesperson/ventas/{venta}/comprobante`.
- [ ] **Task 10**: Create `resources/views/salesperson/ventas/cobro.blade.php` — payment method radios, conditional `monto_recibido` input for `efectivo` with Alpine.js change calc, submit with `wire:loading`-style disabled-on-submit.
- [ ] **Task 11**: Create `resources/views/salesperson/ventas/comprobante.blade.php` — printable layout (`@media print` styles), `window.print()` button, totals, líneas, método, cambio.
- [ ] **Task 12**: Inject `BitacoraServiceInterface` in `VentaService` only for handled failure paths (e.g. stock conflict logged with venta id and producto). No bitácora on happy path.
- [ ] **Task 13**: Bind interface→implementation in `AppServiceProvider` if not already wired.
