# Tasks: Crear Pedido

- [x] **Task 1:** Save spec documentation (this folder).

## Backend

- [ ] **Task 2:** Create `EstadoPedido` backed enum at `app/Enums/EstadoPedido.php` with cases `SOLICITADO`, `ENVIADO`, `RECIBIDO`, `CANCELADO` and a `label()` helper.
- [ ] **Task 3:** Create `Pedido` Eloquent model at `app/Models/Pedido.php` and migration `database/migrations/xxxx_create_pedidos_table.php`. Columns: `cvePedido` (PK), `cveProveedor` (FK), `eEstado` (default `SOLICITADO`), `nTotal` decimal(12,2), `cObservaciones` nullable text, `fEntregaEsperada` nullable date, `cveUsuarioCreador` (FK users), `fCreado` timestamp. Use `casts()` method to cast `eEstado` to `EstadoPedido` and `fEntregaEsperada` to date.
- [ ] **Task 4:** Create `DetallePedido` Eloquent model at `app/Models/DetallePedido.php` and migration `database/migrations/xxxx_create_detalle_pedidos_table.php`. Columns: `cveDetalle` (PK), `cvePedido` (FK pedidos cascade), `cveMedicamento` (FK medicamentos), `nCantidad` int, `nPrecioUnitario` decimal(10,2). Add `unique(['cvePedido', 'cveMedicamento'])` constraint at DB level.
- [ ] **Task 5:** Create `app/Services/Proveedores/Contracts/PedidoServiceInterface.php` exposing `create(array $data, int $usuarioCreadorId): Pedido`.
- [ ] **Task 6:** Implement `app/Services/Proveedores/PedidoService.php`. Wrap creation in `DB::transaction()`: insert `Pedido` with `eEstado = SOLICITADO` and `nTotal` calculated from line subtotals, then insert each `DetallePedido`. Use readonly constructor for any injected dependencies.
- [ ] **Task 7:** Bind `PedidoServiceInterface` to `PedidoService` in `app/Providers/AppServiceProvider.php`.
- [ ] **Task 8:** Create `app/Http/Requests/Proveedores/CreatePedidoRequest.php` with rules:
    - `cveProveedor`: required, exists, proveedor activo
    - `cObservaciones`: nullable, string
    - `fEntregaEsperada`: nullable, date, after_or_equal today
    - `lineas`: required, array, min:1
    - `lineas.*.cveMedicamento`: required, exists, distinct (no duplicates within payload)
    - `lineas.*.nCantidad`: required, integer, min:1
    - `lineas.*.nPrecioUnitario`: required, numeric, min:0
- [ ] **Task 9:** Create `app/Http/Controllers/Web/Proveedores/PedidoController.php` with `create(): View` and `store(CreatePedidoRequest $request): RedirectResponse`. Inject `PedidoServiceInterface` via readonly constructor. Pass `auth()->id()` as `usuarioCreadorId`.

## Routing

- [ ] **Task 10:** Register routes in `routes/web.php` under `middleware(['auth', 'role:inventory_manager'])`:
    - `GET /proveedores/pedidos/create` → `PedidoController@create` (name `proveedores.pedidos.create`)
    - `POST /proveedores/pedidos` → `PedidoController@store` (name `proveedores.pedidos.store`)

## Frontend

- [ ] **Task 11:** Build view at `resources/views/inventory-manager/proveedores/pedidos/create.blade.php`. Master-detail form using Alpine.js for dynamic line addition/removal and live subtotal/total computation. Must use `layouts/app.blade.php` and the inventory-manager nav component. Submit `lineas[]` as nested array. Show validation errors per line.
