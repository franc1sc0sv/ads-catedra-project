# Tasks — Cambiar Contraseña

- [x] **Task 1: Save spec documentation** — `spec.md`, `tasks.md`, `shape.md`, `standards.md`, `references.md` creados en `agent-os/specs/usuarios/cambiar-password/`.

- [x] **Task 2: Extend `UsuarioServiceInterface` and `UsuarioService`**
  - Add `changePassword(User $user, string $currentPassword, string $newPassword): void` — verifica `Hash::check`, asigna `$user->password = $newPassword` (cast `'hashed'` o `Hash::make`), `$user->save()`, luego `Auth::logoutOtherDevices($newPassword)`.
  - Add `resetPasswordByAdmin(User $target, string $newPassword): void` — solo asigna y guarda. NO llama `logoutOtherDevices` (el admin no es dueño de la sesión del target).
  - Ambos métodos firmados en `app/Services/Usuarios/Contracts/UsuarioServiceInterface.php`.
  - Implementación en `app/Services/Usuarios/UsuarioService.php`, `declare(strict_types=1)`, constructor readonly.

- [x] **Task 3: Create `ChangePasswordRequest` (self-change)**
  - `app/Http/Requests/Usuarios/ChangePasswordRequest.php`.
  - `authorize()`: `auth()->check()`.
  - Rules: `current_password` → `['required', 'current_password']`; `password` → `['required', 'string', 'min:8', 'confirmed']`.

- [x] **Task 4: Create `ResetPasswordRequest` (admin reset)**
  - `app/Http/Requests/Usuarios/ResetPasswordRequest.php`.
  - `authorize()`: `auth()->user()?->role === UserRole::Administrator` (o se omite, dado que la ruta ya está bajo `role:administrator`).
  - Rules: `password` → `['required', 'string', 'min:8', 'confirmed']`.

- [ ] **Task 5: Create `PasswordController`**
  - `app/Http/Controllers/Web/Usuarios/PasswordController.php`.
  - Constructor readonly inyecta `UsuarioServiceInterface` y `BitacoraServiceInterface`.
  - `editSelf(): View` → `account.password`.
  - `updateSelf(ChangePasswordRequest): RedirectResponse` → `$service->changePassword(auth()->user(), $validated['current_password'], $validated['password'])`, redirect con flash de éxito.
  - `editForUser(User $usuario): View` → `admin.usuarios.password` con el usuario.
  - `resetForUser(User $usuario, ResetPasswordRequest): RedirectResponse` → `$service->resetPasswordByAdmin($usuario, $validated['password'])`, luego `$bitacora->log('reset_password_admin', $usuario)`, redirect con flash.

- [x] **Task 6: Wire routes in `routes/web.php`**
  - Self-change: `Route::middleware(['auth', 'password.confirm'])->group(function () { Route::get('/account/password', ...)->name('account.password.edit'); Route::put('/account/password', ...)->name('account.password.update'); });`
  - Admin reset: `Route::middleware(['auth', 'role:administrator'])->prefix('admin/usuarios')->group(function () { Route::get('{usuario}/password', ...)->name('admin.usuarios.password.edit'); Route::put('{usuario}/password', ...)->name('admin.usuarios.password.update'); });`

- [x] **Task 7: Build views**
  - `resources/views/account/password.blade.php` — form con `current_password`, `password`, `password_confirmation`. Usa layout `layouts/app`. Componentes UI compartidos.
  - `resources/views/admin/usuarios/password.blade.php` — form con `password`, `password_confirmation` y display del nombre del usuario destino. Usa layout admin.

- [x] **Task 8: Bind interface in `AppServiceProvider`**
  - Si aún no existe, agregar `$this->app->bind(UsuarioServiceInterface::class, UsuarioService::class)` en `AppServiceProvider::register`.

- [ ] **Task 9: Bitácora integration**
  - Confirmar que `BitacoraServiceInterface` expone un método tipo `log(string $accion, Model $entidad)` o equivalente. Si no, alinear con la firma del servicio existente.
  - Solo el reset administrativo se registra. El cambio propio no.
