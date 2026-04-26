---
name: Session Auth (Laravel default)
description: Single auth stack — Laravel session-based authentication for all routes. No JWT, no parallel API stack.
type: project
---

# Session Auth (Laravel default)

> Skills: `/laravel-specialist` for the auth flow and guards, `/security-review` before merging anything that touches credentials or sessions.

Una sola pila de autenticación: sesiones de Laravel para todas las rutas. La sesión vive en la tabla `sessions` que crea el framework — no se modela en el DBML del proyecto.

- `routes/web.php` → middleware `auth` → guard de sesión por defecto.
- No hay `routes/api.php` ni middleware `jwt.auth`. Si en el futuro se expone una API, se usará Laravel Sanctum.

```php
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:administrator'])->group(function () {
    // rutas de admin
});
```

**Login flow:**
- `Auth::attempt($credentials)` valida + inicia sesión.
- `$request->session()->regenerate()` previene fixation.
- Redirección a su dashboard según rol vía `AuthServiceInterface::redirectPathAfterLogin()`.

**Logout flow:**
- `Auth::logout()` + `session()->invalidate()` + `session()->regenerateToken()`.

**Reglas:**
- Los controllers retornan `View|RedirectResponse` — no `JsonResponse`.
- Nunca hashear contraseñas a mano: `Hash::make()` y el cast `'password' => 'hashed'` en el modelo.
- Cambiar la contraseña invalida las demás sesiones del usuario (`Auth::logoutOtherDevices`); la sesión actual sigue activa.
