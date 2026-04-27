# Standards - Alta de Usuario

## authentication/role-middleware

Use `role:<value>` middleware on routes — never check roles inside controllers. The `EnsureRole` middleware reads `auth()->user()->role->value`. For this feature, all routes live under `Route::middleware(['auth', 'role:administrator'])`.

---

## authentication/session-auth

Passwords are hashed via the Eloquent cast `'password' => 'hashed'` declared in `User::casts()`. Assign plain-text passwords on the model; the cast hashes on save. Do not call `Hash::make()` manually in the service when the cast is in place. (Reference pattern: `AuthService` uses `Hash::make` for explicit hashing flows; here the cast handles it.)

---

## backend/php-architecture

Request flow: **Route -> FormRequest -> Controller -> ServiceInterface -> Service -> Model**.

PHP 8.x conventions enforced:
- `declare(strict_types=1)` at the top of every file.
- Readonly constructor promotion for injected dependencies.
- `match` over `switch`.
- Backed enums with a `label()` helper.
- Laravel 12 `casts()` method (not `$casts` property).

---

## backend/service-interface

Every service has a `Contracts/` interface alongside it. Controllers inject the interface, never the concrete class. Bindings live in `AppServiceProvider`.

```
app/Services/Usuarios/
  UsuarioService.php
  Contracts/
    UsuarioServiceInterface.php
```

Controllers only do: validated input -> service call -> return response.

---

## frontend/role-namespacing

Views and nav components are namespaced by role. For administrator-only screens use:

```
resources/views/admin/usuarios/create.blade.php
```

Shared form primitives come from `resources/views/components/ui/`. Layout extends `layouts/app.blade.php`.
