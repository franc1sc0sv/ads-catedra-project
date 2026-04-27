# Standards — Cambiar Contraseña

## authentication/role-middleware

Use `role:<value>` middleware on routes — never check roles inside controllers.

For this feature:

- Self-change route: `auth` + `password.confirm` (no `role:` middleware — cualquier rol autenticado puede cambiar su propia contraseña).
- Admin reset route: `auth` + `role:administrator`.

`EnsureRole` lee `auth()->user()->role->value` y compara contra el parámetro del middleware.

---

## authentication/session-auth

Sesiones de Laravel para todo el módulo web. Sin JWT, sin stack API paralelo.

Para cambio de contraseña:

- `Auth::logoutOtherDevices($newPassword)` invalida cualquier otra sesión del usuario actual y conserva la actual. Solo aplica al cambio propio.
- Hashing: `Hash::make` o el cast `'hashed'` en `User::casts()` (Laravel 12 los hace equivalentes). El modelo `User` ya declara `'password' => 'hashed'`.
- El middleware `password.confirm` fuerza re-confirmación reciente antes de operaciones sensibles. Aplica solo al cambio propio.

---

## backend/php-architecture

Flujo obligatorio: Route → FormRequest → Controller → ServiceInterface → Service → Model.

Para esta feature:

- Routes en `routes/web.php` con middlewares apropiados.
- FormRequests: `ChangePasswordRequest` (self) y `ResetPasswordRequest` (admin) en `app/Http/Requests/Usuarios/`.
- Controller: `PasswordController` en `app/Http/Controllers/Web/Usuarios/` — solo orquesta.
- Service: lógica de hashing, asignación, `logoutOtherDevices` y bitácora vive en `UsuarioService`.
- Model: `User` con cast `'hashed'`.

Todos los archivos PHP nuevos: `declare(strict_types=1);` y promoción readonly del constructor para dependencias inyectadas.

---

## backend/service-interface

Cada servicio tiene una interfaz en `Contracts/` adyacente. El controller inyecta la interfaz, nunca la clase concreta. Los bindings viven en `AppServiceProvider`.

Estructura:

```
app/Services/Usuarios/
  UsuarioService.php
  Contracts/
    UsuarioServiceInterface.php
```

Nuevos métodos a agregar en la interfaz y la implementación:

- `changePassword(User $user, string $currentPassword, string $newPassword): void`
- `resetPasswordByAdmin(User $target, string $newPassword): void`

---

## frontend/role-namespacing

Vistas namespaced por rol cuando son específicas. Vistas compartidas viven bajo un namespace neutral.

Para esta feature:

- Self-change: `resources/views/account/password.blade.php` — namespace `account/` porque cualquier rol autenticado la usa.
- Admin reset: `resources/views/admin/usuarios/password.blade.php` — namespace `admin/usuarios/` porque solo el rol `administrator` la usa.

Componentes compartidos: `resources/views/components/ui/` (button, card, input).
Layouts: `layouts/app.blade.php` para área autenticada general; layout admin si existe uno separado.
