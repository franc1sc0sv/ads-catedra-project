# References - Alta de Usuario

## Codebase

- **`app/Http/Controllers/Web/Auth/AuthController.php`**
  Thin-controller pattern to mirror: readonly constructor injects the service interface, methods receive a typed `FormRequest`, return `View|RedirectResponse`, and delegate all logic to the service. The `UsuarioController` should follow this shape exactly.

- **`app/Services/Auth/AuthService.php`**
  Reference for `Hash::make` usage in the codebase. Note that for alta-usuario we **do not** call `Hash::make` manually — the `User` model's `'password' => 'hashed'` cast handles hashing. AuthService is shown as the canonical hashing reference; the service interface + binding pattern there is what to replicate in `Usuarios/`.

- **`app/Models/User.php`**
  Source of truth for `casts()`: confirms `'role' => UserRole::class` and `'password' => 'hashed'`. Determines that the service can pass a plain string for `password` and a `UserRole` instance (or its value) for `role` and Eloquent will persist correctly.

- **`app/Enums/UserRole.php`**
  Backed enum with `label()` helper. Use `UserRole::cases()` in the Blade `<select>` and `Rule::enum(UserRole::class)` in the FormRequest.

- **`app/Http/Middleware/EnsureRole.php`**
  Middleware backing the `role:<value>` alias used on routes.

- **`app/Providers/AppServiceProvider.php`**
  Where the new `UsuarioServiceInterface -> UsuarioService` binding must be registered.

- **`routes/web.php`**
  Existing `auth` + `role:administrator` group is where the two new routes (`admin.usuarios.create`, `admin.usuarios.store`) attach.

- **`resources/views/layouts/app.blade.php`** and **`resources/views/components/ui/`**
  Layout and shared form primitives reused by the new `create.blade.php`.

## Standards

- `authentication/role-middleware`
- `authentication/session-auth`
- `backend/php-architecture`
- `backend/service-interface`
- `frontend/role-namespacing`
