# Tasks — Activar y Desactivar Cuenta

- [x] Task 1: Save spec documentation (spec.md, shape.md, standards.md, references.md)
- [ ] Task 2: Add `bActiva` boolean column to `users` migration (default `true`) if not present; add `bActiva` to `$fillable` and `casts()` on `App\Models\User`
- [ ] Task 3: Define `toggleActiva(User $user): void` on `App\Services\Usuarios\Contracts\UsuarioServiceInterface` and implement in `App\Services\Usuarios\UsuarioService` (flip `bActiva`, persist)
- [ ] Task 4: Update `App\Services\Auth\AuthService::login` to reject inactive users (`bActiva === false`) with the message "Cuenta suspendida. Contacte al administrador." — verify flag after credential validation, before `Auth::login`
- [ ] Task 5: Add per-request session check (extend `App\Http\Middleware\EnsureRole` or new `EnsureUserActive`): if `auth()->user()->bActiva === false`, call `Auth::logout()`, invalidate session, redirect to login with the suspension message
- [ ] Task 6: Create `App\Http\Controllers\Web\Usuarios\UsuarioController` with `toggleActiva(User $user): RedirectResponse` (thin: receive route model, call service, redirect)
- [ ] Task 7: Register `PATCH /usuarios/{user}/activa` in `routes/web.php` under `auth` + `role:administrator` middleware group, named `admin.usuarios.toggle-activa`
- [ ] Task 8: Add toggle UI control in `resources/views/admin/usuarios/index.blade.php` (form posting via `@method('PATCH')`, button label depends on `bActiva` state, CSRF token)
- [ ] Task 9: Bind `UsuarioServiceInterface` → `UsuarioService` in `App\Providers\AppServiceProvider::register`
