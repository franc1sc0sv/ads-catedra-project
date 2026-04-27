# Standards — Listado de Usuarios

## authentication/role-middleware

`role:<value>` middleware.

Aplicado en la ruta `/admin/usuarios` dentro del grupo `role:administrator`. Nunca chequear roles dentro del controller — la verificación vive en el middleware `EnsureRole` que lee `auth()->user()->role->value`.

---

## authentication/session-auth

Laravel session auth. Add `fUltimoAcceso` column tracked on Auth events.

La columna `fUltimoAcceso` se agrega a la tabla `users` y se actualiza tras un `Auth::attempt()` exitoso en `AuthController::login`. La sesión vive en la tabla `sessions` que crea Laravel — no se modela en el DBML del proyecto.

---

## backend/php-architecture

Route → Controller → ServiceInterface → Service → Model.

Flujo estricto:
- `routes/web.php` → `UsuarioController::index`
- Controller → `UsuarioServiceInterface::list($filters)`
- Service → `User::query()->...->paginate()`
- Sin lógica de filtrado/búsqueda en el controller; solo validación de input + delegación.

---

## backend/service-interface

Service + `Contracts/` interface.

```
app/Services/Usuarios/
  UsuarioService.php
  Contracts/
    UsuarioServiceInterface.php
```

Binding registrado en `App\Providers\AppServiceProvider::register()`. El controller inyecta la interfaz (readonly constructor promotion), nunca la clase concreta.

---

## frontend/role-namespacing

Views: `resources/views/admin/usuarios/`.

La vista del listado vive en `resources/views/admin/usuarios/index.blade.php`. Usa `<x-nav.admin-nav />` y extiende `layouts/app.blade.php`. Componentes UI compartidos (`x-ui.button`, `x-ui.input`, `x-ui.card`) se reutilizan; nada propio del rol fuera de `admin/`.
