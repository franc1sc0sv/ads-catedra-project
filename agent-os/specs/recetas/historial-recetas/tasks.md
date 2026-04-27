# Tasks — Historial de Recetas

- [x] **Task 1: Save spec documentation**
  - Outputs: `spec.md`, `shape.md`, `standards.md`, `references.md` en este folder.

- [ ] **Task 2: Extender RecetaServiceInterface y RecetaService**
  - Agregar método `getHistorial(array $filters, int $perPage): LengthAwarePaginator`.
  - Eager-load de `venta` y `validador` (usuario farmacéutico) para evitar N+1.
  - Aplicar filtros opcionales: estado, medico_id, paciente_id, farmaceutico_id, fecha desde/hasta, número de receta exacto.
  - Orden por defecto: fecha de emisión DESC.

- [ ] **Task 3: RecetaController::historial**
  - Resolver vista por rol: `pharmacist.recetas.historial` o `admin.recetas.historial`.
  - Validar query params (estado, fechas, ids, número).
  - Delegar a `RecetaServiceInterface::getHistorial`.
  - Retornar `View` con paginator + filtros activos para repintar el formulario.

- [ ] **Task 4: Ruta en `routes/web.php`**
  - `GET /recetas/historial` bajo middleware `auth` + `role:pharmacist,administrator`.
  - Nombre: `recetas.historial`.

- [ ] **Task 5: Vista farmacéutico**
  - `resources/views/pharmacist/recetas/historial.blade.php`.
  - Tabla, filtros, búsqueda por número, paginación server-side.
  - Detalle (modal o página dedicada) con `cObservacion` y link a venta vinculada.

- [ ] **Task 6: Vista administrador**
  - `resources/views/admin/recetas/historial.blade.php`.
  - Mismo contenido funcional; layout admin y nav admin.
  - Reutilizar parciales de tabla/filtros si conviene (en `components/`).
