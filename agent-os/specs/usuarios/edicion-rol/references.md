# References — Edición de Cuenta y Rol

## Codebase

- `app/Http/Middleware/EnsureRole.php` — lee `$request->user()?->role?->value` en cada request. Es la pieza clave que permite que el cambio de rol tome efecto en el siguiente request sin force-logout. No requiere modificación.
- `app/Http/Controllers/Web/Auth/AuthController.php` — referencia del patrón thin controller: constructor `readonly` con interfaz inyectada, métodos cortos que delegan al servicio y retornan `View|RedirectResponse`. Replicar el mismo estilo en `Web/Usuarios/UsuarioController`.
- `app/Models/User.php` — modelo con `role` cast a `UserRole` enum y método `casts()` (Laravel 12 style). Sirve de referencia para el binding del enum en validación y vista.
- `app/Enums/UserRole.php` — enum backed con `label()`. Usar `UserRole::cases()` para alimentar el select de la vista y `Rule::enum(UserRole::class)` en `UpdateUsuarioRequest`.
- `app/Services/Bitacora/Contracts/BitacoraServiceInterface.php` (esperado por el spec de bitácora) — se inyecta en `UsuarioService` para registrar `ROL_CAMBIADO` cuando el rol cambia.
- `app/Providers/AppServiceProvider.php` — donde se registran los bindings `Interface => Implementation`. Añadir el binding de `UsuarioServiceInterface`.
- `routes/web.php` — agrupar las rutas `admin/usuarios/*` bajo middleware `['auth', 'role:administrator']`.

## Standards

Ver `standards.md` en este folder para los 5 standards aplicables (role-middleware, session-auth, php-architecture, service-interface, role-namespacing).
