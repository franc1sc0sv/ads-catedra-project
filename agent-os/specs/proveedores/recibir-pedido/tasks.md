# Tasks: Recibir Pedido

- [x] **Task 1: Save spec documentation** — `agent-os/specs/proveedores/recibir-pedido/` con `spec.md`, `shape.md`, `standards.md`, `references.md`, `tasks.md`.

- [ ] **Task 2: Migration — extend `detalle_pedido`**
  - Add `nCantidadRecibida` (integer, nullable, default null).
  - Add `nPrecioReal` (decimal 10,2, nullable, default null).

- [ ] **Task 3: Migration — extend `pedido`**
  - Add `cveUsuarioReceptor` (FK `users.id`, nullable).
  - Add `fRecepcion` (timestamp, nullable).

- [ ] **Task 4: Service — `PedidoServiceInterface::recibir()`**
  - Signature: `recibir(Pedido $pedido, array $lineas, int $cveUsuarioReceptor): Pedido`.
  - `$lineas`: `[cveDetallePedido => ['nCantidadRecibida' => int, 'nPrecioReal' => ?float]]`.

- [ ] **Task 5: Service — `PedidoService::recibir()` implementation**
  - Wrap in `DB::transaction(function () use (...) { ... })`.
  - Validate origin state in `[SOLICITADO, ENVIADO]`; throw domain exception otherwise.
  - Update `Pedido`: `cveEstado = RECIBIDO`, `cveUsuarioReceptor`, `fRecepcion = now()`.
  - Loop lines:
    - Persist `nCantidadRecibida` + `nPrecioReal` (fall back to `nPrecioEstimado` when null).
    - `Medicamento::increment('nStock', nCantidadRecibida)`.
    - Create `MovimientoInventario` `ENTRADA_COMPRA` with `cvePedido` FK.
  - Bind interface in `AppServiceProvider`.

- [ ] **Task 6: FormRequest — `RecibirPedidoRequest`**
  - `lineas` array required.
  - `lineas.*.nCantidadRecibida` required, integer, `min:0`.
  - `lineas.*.nPrecioReal` nullable, numeric, `min:0`.
  - `authorize()` returns true (role gating en middleware).

- [ ] **Task 7: Controller — `PedidoController::recibir`**
  - Namespace `App\Http\Controllers\Web\Dashboard\InventoryManager` (or existing equivalent).
  - `recibir(RecibirPedidoRequest $request, Pedido $pedido): RedirectResponse`.
  - Inject `PedidoServiceInterface` via readonly constructor.
  - `declare(strict_types=1)`. Thin: validated input → service → redirect.

- [ ] **Task 8: Routes — `routes/web.php`**
  - Group `middleware(['auth', 'role:inventory_manager'])`.
  - `GET pedidos/{pedido}/recibir` → form view.
  - `POST pedidos/{pedido}/recibir` → controller `recibir` method.

- [ ] **Task 9: View — `resources/views/inventory-manager/proveedores/pedidos/recibir.blade.php`**
  - Tabla de líneas: medicamento, cantidad solicitada, precio estimado, input `nCantidadRecibida`, input `nPrecioReal`.
  - Botón confirmar con confirm modal.
  - Errores de validación por línea.
