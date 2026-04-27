# Tasks: Cola de Pendientes

- [x] **Task 1 — Save spec documentation**
  - Write `spec.md`, `shape.md`, `standards.md`, `references.md`.

- [ ] **Task 2 — Create `EstadoReceta` enum**
  - File: `app/Enums/EstadoReceta.php`.
  - Backed string enum with cases `PENDIENTE`, `VALIDADA`, `RECHAZADA`.
  - Add `label()` helper for display strings.
  - `declare(strict_types=1)`.

- [ ] **Task 3 — Migration for `recetas` table**
  - Create `database/migrations/<ts>_create_recetas_table.php`.
  - Columns: PK, FKs to `ventas`, `pacientes`, `medicos`, `eEstado` (string cast to enum), `cveRevisorActual` nullable FK to `users`, `fLockExpira` nullable timestamp, `fEmision` timestamp, timestamps.
  - Index `(eEstado, fEmision)` for cola ordering.

- [ ] **Task 4 — `Receta` Eloquent model**
  - File: `app/Models/Receta.php`.
  - `casts()` method (Laravel 12 style) — cast `eEstado` to `EstadoReceta`, `fLockExpira` and `fEmision` to `datetime`.
  - Relations: `venta()`, `paciente()`, `medico()`, `revisor()`, `medicamentos()`.
  - Helper: `isLockActive(): bool` returning `cveRevisorActual !== null && fLockExpira?->isFuture() === true`.

- [ ] **Task 5 — Service contract**
  - File: `app/Services/Recetas/Contracts/RecetaServiceInterface.php`.
  - Method: `getPendientes(?int $cveMedico = null, ?int $cvePaciente = null): Collection`.

- [ ] **Task 6 — Service implementation**
  - File: `app/Services/Recetas/RecetaService.php`.
  - Readonly constructor, `declare(strict_types=1)`.
  - Implements interface; query: `where('eEstado', PENDIENTE)`, optional `where('cveMedico', ...)`, optional `where('cvePaciente', ...)`, `orderBy('fEmision')`, `with(['venta', 'medicamentos', 'paciente', 'medico', 'revisor'])`.
  - Bind interface to implementation in `AppServiceProvider`.

- [ ] **Task 7 — Web controller**
  - File: `app/Http/Controllers/Web/Recetas/RecetaController.php`.
  - Readonly constructor injecting `RecetaServiceInterface`.
  - Action `index(Request $request): View` — read filters from query string, call service, return `view('pharmacist.recetas.cola', ['recetas' => $recetas])`.
  - Return type `View|RedirectResponse` on the class methods.

- [ ] **Task 8 — Routes**
  - In `routes/web.php`, add inside `Route::middleware(['auth', 'role:pharmacist'])`:
    - `Route::get('/recetas/cola', [RecetaController::class, 'index'])->name('pharmacist.recetas.cola');`

- [ ] **Task 9 — Blade view**
  - File: `resources/views/pharmacist/recetas/cola.blade.php`.
  - Extends `layouts.app` with pharmacist nav.
  - Filter form (médico, paciente) submitting via GET.
  - Loop over `$recetas`; render card with paciente, médico, número, badges for medicamentos controlados, tiempo en cola (diff `fEmision` to now).
  - Per card: if `$receta->isLockActive()` show "en revisión por {{ $receta->revisor->name }}" and disable link; otherwise link to validation route placeholder.

- [ ] **Task 10 — Manual smoke check**
  - Run `php artisan migrate`, seed a few recetas in distinct lock states, log in as `pharmacist@pharma.test`, verify ordering, lock display, and filters.
