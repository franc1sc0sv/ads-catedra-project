# Activar y Desactivar Cuenta

## What does this part of the system do?
Es el interruptor que controla si una cuenta puede o no iniciar sesión. Sirve para los casos típicos de la operación: alguien sale de vacaciones largas, deja la farmacia, o simplemente se le suspende el acceso temporalmente mientras se revisa una situación.

Funciona como una suspensión, no como un borrado. La cuenta sigue existiendo en el sistema con todo su histórico intacto: ventas registradas, recetas validadas, movimientos de inventario. Esa trazabilidad es la razón por la que nunca se borra el registro físico.

Reactivar la cuenta es igual de fácil que desactivarla: un solo gesto y la persona vuelve a poder entrar.

## Who uses it?
Solamente el administrador.

## How does it work?
Desde el listado o desde el detalle de un usuario, el admin alterna el estado de la cuenta. Si está activa la marca como inactiva y, a partir de ese instante, los intentos de login con esas credenciales son rechazados con un mensaje claro de que la cuenta está suspendida.

Desactivar una cuenta no borra ningún registro asociado: las ventas que esa persona registró, las recetas que validó, los pedidos que solicitó o recibió y los movimientos de inventario que generó quedan intactos y se siguen pudiendo consultar. La bandera de cuenta inactiva solo bloquea futuros logins, nada más.

Si el usuario tenía sesiones activas en algún navegador, la próxima vez que el sistema verifique la sesión —en cualquier request que haga esa persona— detecta que la cuenta está inactiva, la cierra y la manda al login. Las sesiones existentes no se invalidan instantáneamente en el momento del toggle: es un trade-off aceptado en el MVP, porque en la práctica el siguiente clic basta para sacarla.

Reactivar una cuenta simplemente devuelve el acceso. La contraseña, el rol y todo el histórico se mantienen como estaban; las sesiones anteriores no se reabren solas, la persona vuelve a entrar haciendo login normal.

## Skills relevantes

- `/laravel-specialist` — para el toggle de la bandera de cuenta activa y el guard que bloquea el login de cuentas inactivas.
