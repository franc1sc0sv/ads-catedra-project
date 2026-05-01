# Tasks — Reporte de Inventario

- [x] **Task 1: Save spec documentation** — `spec.md`, `shape.md`, `standards.md`, `references.md` en `agent-os/specs/reportes/reporte-inventario/`.

- [x] **Task 2: Service contract**
  - Crear `app/Services/Reportes/Contracts/ReporteInventarioServiceInterface.php`.
  - Métodos: `computeKPIs(array $filtros): array`, `getRows(array $filtros, int $page = 1): LengthAwarePaginator`, `exportCsv(array $filtros): StreamedResponse`.

- [x] **Task 3: Service implementation**
  - Crear `app/Services/Reportes/ReporteInventarioService.php` (`declare(strict_types=1)`, readonly constructor).
  - `computeKPIs`: 5 agregaciones sobre query filtrada (activos, valor estimado, bajo mínimo, próximos a vencer en ventana, vencidos pendientes).
  - `getRows`: query con joins a proveedor y categoría, fecha de vencimiento más próxima por medicamento, paginación servidor.
  - `exportCsv`: `StreamedResponse`, mismas filas sin paginación, UTF-8 BOM, headers `text/csv`.
  - Helper privado `applyFiltros(Builder $q, array $filtros)` reutilizado por las tres operaciones.

- [x] **Task 4: Service binding**
  - Registrar binding `ReporteInventarioServiceInterface → ReporteInventarioService` en `App\Providers\AppServiceProvider::register()`.

- [x] **Task 5: Controller**
  - Crear `app/Http/Controllers/Web/Reportes/ReporteInventarioController.php`.
  - Constructor inyecta la interface (readonly promotion).
  - `index(Request $request): View` — valida filtros, llama service, devuelve vista por rol.
  - `export(Request $request): StreamedResponse` — valida filtros, delega a `exportCsv`.
  - Sin lógica de negocio; solo validación → service → respuesta.

- [x] **Task 6: Routes**
  - En `routes/web.php`, dentro de `Route::middleware(['auth', 'role:administrator,inventory_manager'])`:
    - `GET /reportes/inventario` → `index` (name `reportes.inventario.index`).
    - `GET /reportes/inventario/export` → `export` (name `reportes.inventario.export`).

- [x] **Task 7: Vistas por rol**
  - `resources/views/admin/reportes/inventario.blade.php`.
  - `resources/views/inventory-manager/reportes/inventario.blade.php`.
  - Cada una extiende `layouts/app.blade.php` con su nav. Contenido común vía partial `resources/views/_partials/reportes/inventario-table.blade.php` (KPIs, form filtros GET, tabla paginada, botón export).

- [x] **Task 8: Verificación manual**
  - Login como admin e inventory_manager → ambos abren la página y ven el mismo contenido. // Ruta gated por `role:administrator,inventory_manager`; `route:list` confirma stack.
  - Cambiar filtros recalcula KPIs y filas.
  - Selector ventana vencimiento (30/60/90) cambia solo el KPI de próximos a vencer.
  - Productos en cero aparecen en tabla.
  - CSV export con filtros aplicados produce el mismo set de filas (sin paginación).
  - Sales y pharmacist son rechazados por `EnsureRole`.
