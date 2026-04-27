# Tasks: historial-compras

## Task 1 — Docs (done)
- [x] spec.md written
- [x] tasks.md written
- [x] shape.md written
- [x] standards.md written
- [x] references.md written

## Task 2 — Service contract
- [ ] Add `getHistorial(Cliente $cliente, int $perPage = 15): LengthAwarePaginator` to `app/Services/Clientes/Contracts/ClienteServiceInterface.php`
- [ ] Implement the method in `app/Services/Clientes/ClienteService.php`
  - Query: `$cliente->ventas()->orderByDesc('fecha')->paginate($perPage)`
  - Do NOT eager-load `detalleVentas` here; keep the list query lean

## Task 3 — Controller method
- [ ] Add `historial(Cliente $cliente): View` to `app/Http/Controllers/Web/Clientes/ClienteController.php`
  - Inject `ClienteServiceInterface`
  - Call `$this->clienteService->getHistorial($cliente)`
  - Return the appropriate view based on the authenticated user's role (or use a shared view with role-specific layout)

## Task 4 — Routes
- [ ] Register route in `routes/web.php` inside the `['auth', 'role:salesperson,administrator']` middleware group:
  ```php
  Route::get('/clientes/{cliente}/historial', [ClienteController::class, 'historial'])->name('clientes.historial');
  ```

## Task 5 — Blade views
- [ ] Create `resources/views/salesperson/clientes/historial.blade.php`
  - Extends `layouts/app.blade.php`
  - Table columns: Fecha, Total, Método de Pago, Estado
  - Cancelled rows: apply visual badge/indicator (e.g. `bg-red-50` row + "Cancelada" badge)
  - Each row links to the sale detail route
  - Pagination: `{{ $ventas->links() }}`
  - Empty state: message when `$ventas->isEmpty()`
- [ ] Create `resources/views/admin/clientes/historial.blade.php`
  - Identical structure; uses admin nav component

## Task 6 — Sale detail route (if not already present)
- [ ] Verify or create `GET /ventas/{venta}` route and controller method
  - Eager-load: `$venta->load('detalleVentas.producto')`
  - Return sale detail view with line items table
