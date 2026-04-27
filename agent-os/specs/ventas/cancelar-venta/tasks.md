# Tasks: Cancelar Venta

- [x] Task 1: Save spec documentation in `agent-os/specs/ventas/cancelar-venta/` (spec.md, tasks.md, shape.md, standards.md, references.md).

- [ ] Task 2: Extend `App\Services\Venta\Contracts\VentaServiceInterface` with two methods:
  - `cancelEnProceso(Venta $venta): void`
  - `cancelCompletada(Venta $venta, string $motivo): void`

- [ ] Task 3: Implement both methods in `App\Services\Venta\VentaService`.
  - `cancelEnProceso`: simple state flip to `CANCELADA`. No transaction, no inventory movements. Log via `BitacoraServiceInterface` with action `VENTA_CANCELADA`.
  - `cancelCompletada`: wrap in `DB::transaction()`. For each `DetalleVenta` line, create a `MovimientoInventario` of type `DEVOLUCION` with `cveVenta` populated, increase stock on the source lot, then mark venta `CANCELADA`. Log via `BitacoraServiceInterface` with action `VENTA_CANCELADA` and the motivo as context.
  - Inject `BitacoraServiceInterface` via readonly constructor promotion.

- [ ] Task 4: Create `App\Http\Requests\Venta\CancelCompletadaRequest` with `motivo` required (string, min length, max length). No request needed for EN_PROCESO flow.

- [ ] Task 5: Add cancel methods to `App\Http\Controllers\Web\Dashboard\VentaController` (or split into salesperson/admin controllers if structure requires):
  - `cancelEnProceso(Venta $venta): RedirectResponse` — calls `cancelEnProceso`, redirects with flash.
  - `showCancelCompletadaForm(Venta $venta): View` — renders the admin cancel form.
  - `cancelCompletada(CancelCompletadaRequest $request, Venta $venta): RedirectResponse` — calls `cancelCompletada($venta, $request->validated('motivo'))`, redirects.

- [ ] Task 6: Add routes in `routes/web.php`:
  - `POST /salesperson/ventas/{venta}/cancel-en-proceso` under `auth` + `role:salesperson`.
  - `GET /admin/ventas/{venta}/cancel` and `POST /admin/ventas/{venta}/cancel` under `auth` + `role:administrator`.

- [ ] Task 7: Create Blade view `resources/views/admin/ventas/cancel-form.blade.php` with `motivo` textarea, submit button, and CSRF token. Use shared `components/ui/` form components.

- [ ] Task 8: Bind `VentaServiceInterface` to `VentaService` in `AppServiceProvider` (if not already bound).

- [ ] Task 9: Confirm `BitacoraServiceInterface` exists and `VENTA_CANCELADA` action enum value is present; add it if missing.

- [ ] Task 10: Run `./vendor/bin/pint` and `composer test` before merging.
