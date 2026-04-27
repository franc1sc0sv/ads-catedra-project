# Tasks — Activar y Desactivar Cuenta

- [x] Task 1: Save spec documentation (spec.md, shape.md, standards.md, references.md)
- [x] Task 2: Add `bActiva` boolean column to `users` migration (default `true`) if not present; add `bActiva` to `$fillable` and `casts()` on `App\Models\User`
- [x] Task 3: Define `toggleActiva(User $user): void` on `App\Services\Usuarios\Contracts\UsuarioServiceInterface` and implement in `App\Services\Usuarios\UsuarioService` (flip `bActiva`, persist)
- [x] Task 4: Update `App\Services\Auth\AuthService::login` to reject inactive users (`bActiva === false`) with the message "Cuenta suspendida. Contacte al administrador." — verify flag after credential validation, before `Auth::login`
- [x] Task 5: Add per-request session check (extend `App\Http\Middleware\EnsureRole` or new `EnsureUserActive`): if `auth()->user()->bActiva === false`, call `Auth::logout()`, invalidate session, redirect to login with the suspension message
- [x] Task 6: Create `App\Http\Controllers\Web\Usuarios\UsuarioController` with `toggleActiva(User $user): RedirectResponse` (thin: receive route model, call service, redirect)
- [x] Task 7: Register `PATCH /usuarios/{user}/activa` in `routes/web.php` under `auth` + `role:administrator` middleware group, named `admin.usuarios.toggle-activa`
- [x] Task 8: Add toggle UI control in `resources/views/admin/usuarios/index.blade.php` (form posting via `@method('PATCH')`, button label depends on `bActiva` state, CSRF token)
- [x] Task 9: Bind `UsuarioServiceInterface` → `UsuarioService` in `App\Providers\AppServiceProvider::register`
