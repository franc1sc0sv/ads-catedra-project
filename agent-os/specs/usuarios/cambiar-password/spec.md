# Cambiar Contraseña

## Resumen

El módulo soporta dos flujos distintos para modificar la contraseña de un usuario, ambos resueltos en HTTP web (sesión Laravel, sin API).

## Flujo 1: Cambio propio (self-change)

Cualquier usuario autenticado puede cambiar su propia contraseña desde su cuenta.

El formulario pide tres campos:

- `current_password` — contraseña actual del usuario.
- `password` — contraseña nueva (mínimo 8 caracteres).
- `password_confirmation` — repetición de la contraseña nueva, debe coincidir.

El sistema valida:

1. Que la contraseña actual sea correcta (regla `current_password` de Laravel).
2. Que la nueva tenga al menos 8 caracteres.
3. Que `password` y `password_confirmation` coincidan (regla `confirmed`).

Tras éxito, el servicio hashea la nueva contraseña con `Hash::make` (el cast `'hashed'` del modelo también lo cubre) y ejecuta `Auth::logoutOtherDevices($newPassword)`. Esto invalida cualquier otra sesión activa del usuario en otros dispositivos pero conserva la sesión actual.

La ruta de cambio propio se protege con el middleware `password.confirm` además de `auth`. Esto fuerza una reconfirmación de identidad reciente antes de permitir el cambio.

Este flujo NO se registra en bitácora — es una acción rutinaria de cuenta.

## Flujo 2: Reset administrativo

Solo un usuario con rol `administrator` puede resetear la contraseña de otro usuario.

El formulario pide únicamente:

- `password` — contraseña nueva (mínimo 8 caracteres).
- `password_confirmation` — repetición.

No se pide la contraseña actual del usuario destino — el admin no la conoce.

Tras éxito, el servicio hashea y guarda. NO se ejecuta `Auth::logoutOtherDevices` porque el admin no está autenticado como ese usuario. Las sesiones del usuario destino quedan intactas (se invalidarán naturalmente al re-autenticar con la contraseña vieja, que ya no funciona).

Esta acción SÍ se registra en bitácora a través de `BitacoraServiceInterface` con `accion = 'reset_password_admin'` y la entidad afectada (id del usuario destino).

## Convenciones

- Service-interface: la lógica vive en `UsuarioService` con dos métodos nuevos en su contrato.
- Controllers thin: `PasswordController` solo orquesta — valida via FormRequest, llama servicio, retorna `View|RedirectResponse`.
- `declare(strict_types=1)` en todos los archivos PHP nuevos.
- Promoción readonly del constructor para inyectar `UsuarioServiceInterface` y `BitacoraServiceInterface`.
