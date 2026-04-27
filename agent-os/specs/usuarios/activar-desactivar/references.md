# References — Activar y Desactivar Cuenta

## Codebase

- `app/Http/Controllers/Web/Auth/AuthController.php` — patrón de controlador delgado a replicar en `UsuarioController::toggleActiva`: validar/recibir input, delegar al servicio, devolver `RedirectResponse`.
- `app/Models/User.php` — modelo donde se agrega la columna `bActiva` (fillable + casts). También donde el guard de sesión leerá el flag.
- `app/Services/Auth/AuthService.php` (y su `Contracts/AuthServiceInterface.php`) — punto de modificación para rechazar credenciales válidas cuando `bActiva === false`. Patrón a seguir para crear `UsuarioService` + `UsuarioServiceInterface`.
- `app/Http/Middleware/EnsureRole.php` — middleware donde se puede injertar la verificación de `bActiva` o, mejor, crear un middleware hermano `EnsureUserActive` que se aplica antes del role check.
- `app/Providers/AppServiceProvider.php` — donde se vincula `UsuarioServiceInterface` a `UsuarioService`.
- `app/Enums/UserRole.php` — enum de roles; `administrator` es el único habilitado para esta acción vía `role:administrator`.
- `routes/web.php` — registro de la ruta `PATCH /usuarios/{user}/activa` dentro del grupo `auth` + `role:administrator`.

## Producto

- Plan-product MVP, sección 2 — Gestión de Usuarios. Define el alcance: alta, edición, suspensión sin borrado.

## Cuentas seedeadas para pruebas

- `admin@pharma.test` (administrator) — único que puede ejecutar el toggle.
- `sales@pharma.test`, `inventory@pharma.test`, `pharmacist@pharma.test` — sujetos de prueba para desactivar y verificar que el login se rechaza y la sesión activa se cierra en el siguiente request.
