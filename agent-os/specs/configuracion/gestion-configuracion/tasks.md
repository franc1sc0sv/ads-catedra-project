# Tasks: Gestión de Configuración Global

## Task 1 — Spec & Docs [DONE]

- [x] spec.md written
- [x] tasks.md written
- [x] shape.md written
- [x] standards.md written
- [x] references.md written

---

## Task 2 — Model, Migration & Seeder

- [ ] Create migration: `create_cat_configuracion_table`
  - Columns: `id`, `cClave` (unique), `cValor`, `eTipoDato` (enum), `bEditable` (bool, default true), `cDescripcion`, `fActualizado` (timestamp, nullable)
- [ ] Create `app/Models/CatConfiguracion.php`
  - `declare(strict_types=1)`
  - Readonly constructor not applicable (Eloquent model); use `$fillable` + `casts()` method
  - Cast `bEditable` to bool in `casts()`
  - `$timestamps = false` — only `fActualizado` is managed manually
- [ ] Create `database/seeders/CatConfiguracionSeeder.php`
  - Upsert `dias_alerta_vencimiento` (INTEGER, `30`, bEditable=true)
  - Upsert `umbral_aviso_stock_bajo` (INTEGER, `0`, bEditable=true)
  - Register in `DatabaseSeeder`

---

## Task 3 — Service Interface & Service

- [ ] Create `app/Services/Configuracion/Contracts/ConfiguracionServiceInterface.php`
  ```php
  public function getValue(string $key, mixed $default = null): mixed;
  public function update(string $key, mixed $value): void;
  public function allEditable(): Collection;
  ```
- [ ] Create `app/Services/Configuracion/ConfiguracionService.php`
  - `declare(strict_types=1)`
  - Readonly constructor injection of nothing (queries model directly — no repo layer needed at MVP)
  - `getValue`: query by `cClave`, cast result via `eTipoDato` match, return `$default` if not found
  - `update`: find by `cClave`, assert `bEditable`, set `cValor = (string) $value`, set `fActualizado = now()`, save
  - `allEditable`: return all rows ordered by `cClave`
  - Cast helper uses `match` over `switch`
- [ ] Bind interface → concrete in `AppServiceProvider`

---

## Task 4 — Form Request

- [ ] Create `app/Http/Requests/Configuracion/UpdateConfiguracionRequest.php`
  - `authorize()` returns `true` (route middleware already guards)
  - `rules()`: `['valor' => 'required|string|max:255']`

---

## Task 5 — Controller

- [ ] Create `app/Http/Controllers/Web/Configuracion/ConfiguracionController.php`
  - `declare(strict_types=1)`
  - Readonly constructor: inject `ConfiguracionServiceInterface`
  - `index(): View` — pass `$configs = $service->allEditable()` to view
  - `update(UpdateConfiguracionRequest $request, string $clave): RedirectResponse`
    - Call `$service->update($clave, $request->validated('valor'))`
    - Redirect back with success flash

---

## Task 6 — Routes

- [ ] Add to `routes/web.php` inside `['auth', 'role:administrator']` group:
  ```php
  Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
  Route::patch('/configuracion/{clave}', [ConfiguracionController::class, 'update'])->name('configuracion.update');
  ```

---

## Task 7 — View

- [ ] Create `resources/views/admin/configuracion/index.blade.php`
  - Extends `layouts/app.blade.php`
  - Table with columns: Clave, Descripcion, Tipo, Valor, Acciones
  - For each config row:
    - If `bEditable`: show inline form with type-appropriate input + save button
    - If not `bEditable`: show value as plain text, no form
  - Flash success message display
  - Use shared UI components (`components/ui/button`, `components/ui/card`, `components/ui/input`) where they exist

---

## Task 8 — Admin Nav Link

- [ ] Add "Configuración" link to `resources/views/components/nav/admin-nav.blade.php`
  - Route: `configuracion.index`
