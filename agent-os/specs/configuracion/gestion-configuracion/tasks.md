# Tasks: Gestión de Configuración Global

## Task 1 — Spec & Docs [DONE]

- [x] spec.md written
- [x] tasks.md written
- [x] shape.md written
- [x] standards.md written
- [x] references.md written

---

## Task 2 — Model, Migration & Seeder [DONE — via existing Setting infrastructure]

The codebase has already drifted from the c-prefix DBML naming. Equivalent infrastructure already exists with English snake_case names; we use that instead of creating duplicate `CatConfiguracion` artifacts.

- [x] Migration `create_settings_table` (columns `id`, `key` unique, `value`, `description`, `data_type`, `editable` bool default true, `created_at`, `updated_at`).
- [x] `app/Models/Setting.php` with `$fillable`, `casts()` (uses `App\Enums\SettingType`), `typedValue()` helper.
- [x] `database/seeders/SettingSeeder.php` upserts `dias_alerta_vencimiento` (and 6 additional keys); registered in `DatabaseSeeder`.

---

## Task 3 — Service Interface & Service

- [x] Create `app/Services/Configuracion/Contracts/ConfiguracionServiceInterface.php`
  ```php
  public function getValue(string $key, mixed $default = null): mixed;
  public function update(string $key, mixed $value): void;
  public function allEditable(): Collection;
  ```
- [x] Create `app/Services/Configuracion/ConfiguracionService.php`
  - `declare(strict_types=1)`
  - Readonly constructor injection of nothing (queries model directly — no repo layer needed at MVP)
  - `getValue`: query by `cClave`, cast result via `eTipoDato` match, return `$default` if not found
  - `update`: find by `cClave`, assert `bEditable`, set `cValor = (string) $value`, set `fActualizado = now()`, save
  - `allEditable`: return all rows ordered by `cClave`
  - Cast helper uses `match` over `switch`
- [x] Bind interface → concrete in `AppServiceProvider`

---

## Task 4 — Form Request

- [x] Create `app/Http/Requests/Configuracion/UpdateConfiguracionRequest.php`
  - `authorize()` returns `true` (route middleware already guards)
  - `rules()`: `['valor' => 'required|string|max:255']`

---

## Task 5 — Controller

- [x] Create `app/Http/Controllers/Web/Configuracion/ConfiguracionController.php`
  - `declare(strict_types=1)`
  - Readonly constructor: inject `ConfiguracionServiceInterface`
  - `index(): View` — pass `$configs = $service->allEditable()` to view
  - `update(UpdateConfiguracionRequest $request, string $clave): RedirectResponse`
    - Call `$service->update($clave, $request->validated('valor'))`
    - Redirect back with success flash

---

## Task 6 — Routes

- [x] Add to `routes/web.php` inside `['auth', 'role:administrator']` group:
  ```php
  Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
  Route::patch('/configuracion/{clave}', [ConfiguracionController::class, 'update'])->name('configuracion.update');
  ```

---

## Task 7 — View

- [x] Create `resources/views/admin/configuracion/index.blade.php`
  - Extends `layouts/app.blade.php`
  - Table with columns: Clave, Descripcion, Tipo, Valor, Acciones
  - For each config row:
    - If `bEditable`: show inline form with type-appropriate input + save button
    - If not `bEditable`: show value as plain text, no form
  - Flash success message display
  - Use shared UI components (`components/ui/button`, `components/ui/card`, `components/ui/input`) where they exist

---

## Task 8 — Admin Nav Link

- [x] Add "Configuración" link to `resources/views/components/nav/admin-nav.blade.php`
  - Route: `configuracion.index`
