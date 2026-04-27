# Tasks — Catálogo de Medicamentos

- [x] Task 1: Save spec documentation (done)
- [x] Task 2: Create `App\Enums\CategoriaMedicamento` backed enum (`venta_libre`, `requiere_receta`, `controlado`) with `label()` helper at `app/Enums/CategoriaMedicamento.php`
- [x] Task 3: Generate migration `create_medicamentos_table` with all fields (`cNombre`, `cDescripcion`, `cCodigoBarras` UNIQUE, `nPrecio`, `nStockActual`, `nStockMinimo`, `dFechaVencimiento`, `eCategoria`, `idProveedor` FK, `bActivo` default true, timestamps)
- [x] Task 4: Create `App\Models\Medicamento` with `casts()` for enum + date, relations `proveedor()` and `movimientos()`, scope `activos()`
- [x] Task 5: Create `app/Services/Inventario/Contracts/MedicamentoServiceInterface.php` with: `listar(array $filters)`, `crear(array $data)`, `actualizar(Medicamento $m, array $data)`, `desactivar(Medicamento $m)`, `reactivar(Medicamento $m)`, `estaVencido(Medicamento $m): bool`
- [x] Task 6: Implement `app/Services/Inventario/MedicamentoService.php` — readonly constructor, `crear()` wraps in DB transaction and auto-creates `ajuste_manual` movement when `stock_inicial > 0`; `desactivar()` guards against active EN_PROCESO sales
- [x] Task 7: Bind interface → concrete in `AppServiceProvider`
- [x] Task 8: Create `app/Http/Requests/Inventario/CreateMedicamentoRequest.php` and `UpdateMedicamentoRequest.php` with unique `cCodigoBarras` rule (ignore self on update)
- [x] Task 9: Create `app/Http/Controllers/Web/Inventario/MedicamentoController.php` — thin controller, returns `View|RedirectResponse`, injects `MedicamentoServiceInterface`
- [x] Task 10: Register routes in `routes/web.php`:
  - Read (`index`, `show`): `middleware(['auth', 'role:inventory_manager,salesperson,pharmacist'])`
  - Write (`create`, `store`, `edit`, `update`, `destroy`, `restore`): `middleware(['auth', 'role:inventory_manager'])`
- [x] Task 11: Build write views at `resources/views/inventory-manager/inventario/medicamentos/{index,create,edit,show}.blade.php`
- [x] Task 12: Build read-only views at `resources/views/salesperson/inventario/medicamentos/{index,show}.blade.php` and `resources/views/pharmacist/inventario/medicamentos/{index,show}.blade.php` (or share partial with conditional edit controls gated on `auth()->user()->role`)
- [x] Task 13: Seed `MedicamentoSeeder` with sample rows across all three categorías
- [ ] Task 14: Feature tests — unique barcode rejection, auto-movement on stock_inicial>0, vencido check returns true past date, desactivar blocked when EN_PROCESO sale exists, role middleware enforces read/write split
