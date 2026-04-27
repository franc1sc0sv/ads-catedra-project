# References — Cambiar Contraseña

## Codebase

### `app/Http/Controllers/Web/Auth/AuthController.php`

Patrón de controller thin a replicar. Inyecta `AuthServiceInterface` por constructor readonly, valida via FormRequest, llama servicio, retorna `View|RedirectResponse`. Sin lógica de negocio. `PasswordController` debe seguir la misma forma.

### `app/Services/Auth/AuthService.php`

Patrón de servicio: uso de `Hash::make`, manejo de la sesión de Laravel, `declare(strict_types=1)`, constructor readonly. Las nuevas operaciones de password en `UsuarioService` deben mirar acá para consistencia (especialmente cómo se invocan los helpers de `Auth::` y se manipulan campos hasheados).

### `app/Models/User.php`

El modelo ya declara `'password' => 'hashed'` en su método `casts()`. Esto hace que asignar texto plano a `$user->password` lo hashee automáticamente al persistir. Igual conviene usar `Hash::make` explícito en el servicio para que la intención quede clara y para alinear con la firma de `Auth::logoutOtherDevices($plainPassword)` que necesita el plano.

### `app/Enums/UserRole.php`

Define `administrator`, `salesperson`, `inventory_manager`, `pharmacist`. Solo `administrator` puede acceder al flujo de reset. El middleware `role:administrator` hace cumplir esto a nivel ruta.

### `app/Http/Middleware/EnsureRole.php` (o equivalente)

Lee `auth()->user()->role->value` y compara contra el parámetro. Ya existe — esta feature lo consume, no lo modifica.

### Bitácora

Esta feature consume `BitacoraServiceInterface` para registrar el reset administrativo. Antes de implementar Task 5/9, confirmar la firma exacta del método de logging (`log`, `record`, `escribir`, etc.) en el contrato existente.

## Laravel docs

### `Illuminate\Support\Facades\Auth::logoutOtherDevices($password)`

Re-hashea el `remember_token` y el password hash en la sesión actual de forma que cualquier otra sesión activa con el hash anterior queda inválida en su próximo request. El parámetro `$password` es el texto plano nuevo. Requiere que el `EnsureSessionsMatchPasswords` middleware esté en `app/Http/Kernel.php` (Laravel 12 lo incluye por defecto en el grupo `web` mediante `AuthenticateSession`).

### Middleware `password.confirm`

Definido en `Illuminate\Auth\Middleware\RequirePassword`. Antes de la ruta protegida, redirige a `password.confirm` (vista incluida en starter kits) si el usuario no ha confirmado su contraseña en los últimos N minutos (config en `auth.password_timeout`, default 10800s = 3 hrs). Útil para acciones sensibles aunque la sesión ya esté autenticada.

### Regla de validación `current_password`

Verifica que el campo coincida con la contraseña actual del `auth()->user()`. Reemplaza el patrón manual de `Hash::check`. Acepta un guard opcional: `current_password:web`.

### Regla `confirmed`

Requiere un campo adicional `<field>_confirmation` con el mismo valor. Usado para `password_confirmation` en ambos flujos.
