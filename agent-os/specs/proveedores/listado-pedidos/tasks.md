# Tasks — Listado de Pedidos

- [x] Task 1: Save spec documentation
- [x] Task 2: Extend `PedidoServiceInterface` and `PedidoService` with `list(array $filters)`, `cancel(Pedido $pedido, string $motivo)`, `markEnviado(Pedido $pedido)`. Enforce state machine inside the service (cancel only from `SOLICITADO`; markEnviado only from `SOLICITADO`).
- [x] Task 3: Create `App\Http\Requests\Proveedores\CancelPedidoRequest` with `motivo` required (string, min length, max length).
- [x] Task 4: Add `PedidoController` methods:
  - `index(Request $request): View` — read filters, call service, return paginated list.
  - `show(Pedido $pedido): View` — eager-load proveedor + lineas, return detail view.
  - `cancel(CancelPedidoRequest $request, Pedido $pedido): RedirectResponse` — call service, flash result.
  - `send(Pedido $pedido): RedirectResponse` — call `markEnviado`, flash result.
- [x] Task 5: Register routes in `routes/web.php` under `auth` + `role:inventory_manager,administrator` for index/show; restrict `cancel` and `send` to `role:inventory_manager`.
- [x] Task 6: Build `resources/views/inventory-manager/proveedores/pedidos/index.blade.php` and `show.blade.php` (filters form, table, detail with action buttons + motivo modal).
- [ ] Task 7: Build read-only counterpart `resources/views/admin/proveedores/pedidos/index.blade.php` and `show.blade.php` (no action buttons).
- [x] Task 8: Bind `PedidoServiceInterface` to `PedidoService` in `AppServiceProvider` if not already bound.
