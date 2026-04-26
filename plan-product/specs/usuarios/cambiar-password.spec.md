# Cambiar Contraseña

## What does this part of the system do?
Es la pantalla que cualquier usuario autenticado abre desde su menú cuando quiere actualizar su contraseña. Está pensada como una herramienta de auto-servicio para que nadie tenga que pedirle al admin algo tan básico como cambiar su clave personal.

La misma pantalla, vista desde el panel del administrador, sirve también para resetear la contraseña de otra persona. Esto cubre el caso de los olvidos, que en el MVP no tienen recuperación por correo.

El detalle más importante es que, después de cambiar la contraseña, el sistema fuerza a que cualquier otra sesión abierta de esa cuenta tenga que volver a iniciar sesión con la nueva clave.

## Who uses it?
Cualquier usuario autenticado para su propia contraseña, y adicionalmente el administrador para resetear la de otros.

## How does it work?
Cuando un usuario cambia la suya, ingresa la contraseña actual y la nueva escrita dos veces. El sistema valida primero que la actual sea correcta; si no lo es, rechaza el cambio con un mensaje claro y no toca nada. También exige que la nueva tenga la longitud mínima requerida y que las dos copias coincidan entre sí. Cuando es el administrador quien resetea la clave de otra persona, el flujo se simplifica: no se pide la contraseña actual, solo la nueva, y queda registrado en la bitácora general que fue un reset hecho por un admin.

Tras un cambio exitoso, el sistema invalida todas las otras sesiones del usuario —las que están abiertas en otros navegadores o dispositivos— y conserva solo la sesión actual, para no expulsar a quien acaba de hacer el cambio. Esto se apoya en el manejo de sesiones nativo del framework; no existe una tabla custom de tokens detrás de esta función.

## Skills relevantes

- `/laravel-specialist` — para el form request, el update del hash y la invalidación de las otras sesiones activas.
- `/security-review` — para confirmar que el reset hecho por un admin queda asentado en la bitácora general.
