# Tasks — Adjuntar Receta

- [x] **Task 1**: Save spec documentation (spec.md, shape.md, standards.md, references.md, tasks.md).

- [ ] **Task 2**: `VentaReceta` model + migration.
  - File: `database/migrations/xxxx_create_venta_recetas_table.php`
  - Columns: `cveVenta` (FK ventas), `cveMedicamento` (FK medicamentos), `cveReceta` (FK recetas), timestamps.
  - Unique index on `(cveVenta, cveMedicamento)`.
  - File: `app/Models/VentaReceta.php` — `declare(strict_types=1)`, `casts()` method.
  - Add relationships: `Venta::ventaRecetas()`, `Receta::ventaRecetas()`.

- [ ] **Task 3**: Extend `RecetaServiceInterface` with `findReusableForVenta`.
  - File: `app/Services/Recetas/Contracts/RecetaServiceInterface.php`
  - Signature: `findReusableForVenta(Venta $venta, Medicamento $medicamento, string $cNombrePaciente): \Illuminate\Support\Collection`.
  - File: `app/Services/Recetas/RecetaService.php`
  - Apply 4 conditions: estado=VALIDADA, no ligada a venta no-CANCELADA, cveMedicamento coincide, cNombrePaciente coincide.

- [ ] **Task 4**: Extend `VentaServiceInterface` with attach methods + cobro gate.
  - File: `app/Services/Ventas/Contracts/VentaServiceInterface.php`
  - Methods:
    - `attachReceta(Venta $venta, Medicamento $medicamento, array $datosReceta): VentaReceta` — crea Receta PENDIENTE + fila pivot.
    - `attachExistingReceta(Venta $venta, Medicamento $medicamento, Receta $receta): VentaReceta` — crea fila pivot apuntando a receta existente; valida re-elegibilidad antes.
    - `isCobrableAhora(Venta $venta): bool` — true si todo controlado de la venta tiene receta VALIDADA.
  - File: `app/Services/Ventas/VentaService.php`
  - Envolver attach* en `DB::transaction`.

- [ ] **Task 5**: `AdjuntarRecetaRequest` (FormRequest).
  - File: `app/Http/Requests/Ventas/AdjuntarRecetaRequest.php`
  - Reglas para ambos caminos: `mode in:new,reuse`, campos de receta para `new`, `cveReceta` para `reuse`, `cveMedicamento` siempre.

- [ ] **Task 6**: `VentaController` (Web/Ventas).
  - File: `app/Http/Controllers/Web/Ventas/VentaController.php`
  - Métodos: `showAdjuntarReceta`, `attachReceta`, `attachExistingReceta` (delegan al service; devuelven `View|RedirectResponse`).

- [ ] **Task 7**: Routes.
  - File: `routes/web.php`
  - Grupo `auth + role:salesperson`:
    - GET `/ventas/{venta}/adjuntar-receta/{medicamento}` → `showAdjuntarReceta`
    - POST `/ventas/{venta}/adjuntar-receta/{medicamento}/new` → `attachReceta`
    - POST `/ventas/{venta}/adjuntar-receta/{medicamento}/reuse` → `attachExistingReceta`

- [ ] **Task 8**: View con dos pestañas.
  - File: `resources/views/salesperson/ventas/adjuntar-receta.blade.php`
  - Tab 1 "Capturar nueva": form con datos de receta (paciente, médico, fecha).
  - Tab 2 "Reutilizar del histórico": listado del resultado de `findReusableForVenta` con botón "Vincular".
  - Layout `layouts/app.blade.php`, nav `salesperson-nav`.
