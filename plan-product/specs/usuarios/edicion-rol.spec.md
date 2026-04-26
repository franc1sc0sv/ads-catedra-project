# Edición de Cuenta y Rol

## What does this part of the system do?
Es la pantalla de edición de una cuenta existente. Permite al administrador corregir el nombre, actualizar el correo o reasignar el rol de una persona cuando sus responsabilidades cambian dentro de la farmacia.

El cambio de rol es la parte más sensible: define a qué áreas del sistema entra esa persona desde ese momento en adelante. Por eso la pantalla muestra con claridad qué rol tiene actualmente la cuenta y cuál se va a guardar.

La intención es que el admin pueda mantener al día la estructura de accesos sin tener que borrar y recrear cuentas.

## Who uses it?
Solamente el administrador.

## How does it work?
El admin abre un usuario existente desde el listado y puede cambiar nombre, correo o rol. El cambio se guarda al instante; si tocó el correo, el sistema vuelve a validar que no choque con otra cuenta antes de aceptar.

El cambio de rol toma efecto en el siguiente request del usuario afectado. Si esa persona está parada en una pantalla cuando el admin guarda, no la expulsa de inmediato; en cuanto haga clic en cualquier acción, el middleware lee el rol nuevo y, si la acción no corresponde a ese rol, la redirige al dashboard que sí le toca. No hay que pedirle que cierre sesión y vuelva a entrar.

## Skills relevantes

- `/laravel-specialist` — para el form request y el update del modelo de usuario.
- `/security-review` — para verificar que un admin no pueda escalar privilegios sin que quede rastro en la bitácora general, evitando rutas de escalación de permisos no auditadas.
