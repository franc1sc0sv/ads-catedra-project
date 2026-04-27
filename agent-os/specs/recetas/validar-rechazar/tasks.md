# Tasks — Validar o Rechazar Receta

- [x] **Task 1:** Save spec documentation in `agent-os/specs/recetas/validar-rechazar/`.

- [ ] **Task 2:** Migration — extender tabla `recetas`
  - Agregar columnas: `cveValidador` (FK nullable a `usuarios`), `fValidacion` (timestamp nullable), `cObservacion` (text nullable).
  - Asegurar que `cveRevisorActual` y `fLockExpira` ya existan (de la feature de carga de receta); si no, agregarlos.
  - Index en `(estado, fLockExpira)` para queries de locks activos.

- [ ] **Task 3:** `App\Services\Recetas\Contracts\RecetaServiceInterface`
  - `acquireLock(int $recetaId, int $userId): RecetaLockResult` — adquiere o reclama lock expirado; devuelve estado del lock (held_by_self | held_by_other | acquired).
  - `decidir(int $recetaId, int $userId, RecetaDecisionDTO $dto): void` — aplica decisión + limpia lock + auto-unlock de venta si corresponde.

- [ ] **Task 4:** `App\Services\Recetas\RecetaService`
  - Implementa interface. Toda operación dentro de `DB::transaction` con `lockForUpdate()` sobre la fila de receta.
  - `decidir`: valida estado `PENDIENTE` y propiedad del lock; al validar, consulta `venta_receta` y verifica que todas las recetas asociadas a la venta estén `VALIDADA` antes de desbloquear la venta.
  - Binding en `AppServiceProvider`.

- [ ] **Task 5:** `App\Http\Requests\Recetas\DecidirRecetaRequest`
  - `decision`: `required|in:validada,rechazada`.
  - `observacion`: `required_if:decision,rechazada|nullable|string|max:1000`.
  - `authorize()`: `auth()->user()->role->value === 'pharmacist'`.

- [ ] **Task 6:** `App\Http\Controllers\Web\Pharmacist\RecetaController`
  - `show(int $id)`: llama `acquireLock`, según resultado renderiza `pharmacist.recetas.revision` o vista de "ocupada".
  - `decidir(DecidirRecetaRequest $request, int $id)`: invoca `decidir`, redirect a listado con flash success.
  - Thin controller — solo input validado → service → response.

- [ ] **Task 7:** `routes/web.php`
  - Grupo `auth` + `role:pharmacist` con prefix `pharmacist/recetas`.
  - `GET /{id}/revision` → `RecetaController@show`.
  - `POST /{id}/decision` → `RecetaController@decidir`.

- [ ] **Task 8:** Vista `resources/views/pharmacist/recetas/revision.blade.php`
  - Muestra datos de la receta (paciente, médico, productos asociados a la venta).
  - Form con radio `decision` (validada/rechazada), textarea `observacion`, botón submit.
  - Banner con countdown de `fLockExpira` (informativo).
  - Vista alternativa cuando lock pertenece a otro: "Esta receta está siendo revisada por X".
