# Tasks: Catálogo de Clientes

## Task 1 — Spec & Documentation (done)
- [x] Write `spec.md`, `shape.md`, `standards.md`, `references.md`, `tasks.md`

## Task 2 — Migration & Model
- [ ] Create migration `create_clientes_table` with columns: `id`, `nombre`, `telefono`, `correo`, `direccion`, `identificacion` (unique), `es_frecuente` (boolean, default false), `bActivo` (boolean, default true), `timestamps`
- [ ] Create `app/Models/Cliente.php` with `$fillable`, `casts()` returning `['es_frecuente' => 'boolean', 'bActivo' => 'boolean']`, no SoftDeletes trait
- [ ] Add unique index on `identificacion` in migration

## Task 3 — Service Interface
- [ ] Create `app/Services/Clientes/Contracts/ClienteServiceInterface.php`
  - Methods: `list(array $filters): LengthAwarePaginator`, `create(array $data): Cliente`, `update(Cliente $cliente, array $data): Cliente`, `deactivate(Cliente $cliente): void`, `restore(Cliente $cliente): void`

## Task 4 — Service Implementation
- [ ] Create `app/Services/Clientes/ClienteService.php` implementing `ClienteServiceInterface`
  - `list`: query `bActivo = true`, apply `ILIKE` search on `nombre`/`identificacion` if `q` present, paginate 15
  - `create`: `Cliente::create($data)`
  - `update`: check `$cliente->ventas()->exists()` — if true, unset `identificacion` from `$data`; then `$cliente->update($data)`
  - `deactivate`: `$cliente->update(['bActivo' => false])`
  - `restore`: `$cliente->update(['bActivo' => true])`
- [ ] Bind interface to implementation in `AppServiceProvider`

## Task 5 — Form Requests
- [ ] Create `app/Http/Requests/Clientes/CreateClienteRequest.php`
  - Rules: `nombre` required string, `identificacion` required string unique:clientes, `telefono` nullable string, `correo` nullable email, `direccion` nullable string, `es_frecuente` boolean
- [ ] Create `app/Http/Requests/Clientes/UpdateClienteRequest.php`
  - Rules: same as create but `sometimes` on all fields; `identificacion` uses `unique:clientes,identificacion,{id}` ignore rule

## Task 6 — Controller
- [ ] Create `app/Http/Controllers/Web/Clientes/ClienteController.php`
  - Inject `ClienteServiceInterface` via readonly constructor promotion
  - `index(Request $request)`: call `service->list(['q' => $request->q])`, resolve view by role
  - `create()`: return role-namespaced view
  - `store(CreateClienteRequest $request)`: call `service->create($request->validated())`, redirect to index with success flash
  - `edit(Cliente $cliente)`: return role-namespaced view with cliente
  - `update(UpdateClienteRequest $request, Cliente $cliente)`: call `service->update(...)`, redirect to index
  - `destroy(Cliente $cliente)`: call `service->deactivate(...)`, redirect to index
  - `restore(Cliente $cliente)`: call `service->restore(...)`, redirect to index
  - Helper method `resolveView(string $view)` that uses `match(auth()->user()->role->value)` to return the correct namespaced path

## Task 7 — Routes
- [ ] Add to `routes/web.php` under `middleware(['auth', 'role:salesperson,administrator'])`:
  - `Route::resource('clientes', ClienteController::class)->except(['show'])`
  - `Route::patch('clientes/{cliente}/restore', [ClienteController::class, 'restore'])->name('clientes.restore')`

## Task 8 — Views (salesperson)
- [ ] `resources/views/salesperson/clientes/index.blade.php` — table with search input, bActivo rows, deactivate/restore buttons
- [ ] `resources/views/salesperson/clientes/create.blade.php` — form using `ui.input`, `ui.button`
- [ ] `resources/views/salesperson/clientes/edit.blade.php` — same as create, pre-filled; identification field disabled if ventas exist (pass `$hasVentas` from controller)

## Task 9 — Views (admin)
- [ ] `resources/views/admin/clientes/index.blade.php`
- [ ] `resources/views/admin/clientes/create.blade.php`
- [ ] `resources/views/admin/clientes/edit.blade.php`

## Task 10 — Tests
- [ ] Feature test: create client with unique identificacion, expect redirect
- [ ] Feature test: create client with duplicate identificacion, expect validation error
- [ ] Feature test: update client identificacion when ventas exist, expect identificacion unchanged
- [ ] Feature test: deactivate client, expect bActivo = false and absent from index search
- [ ] Feature test: restore client, expect bActivo = true
