# Activar y Desactivar Cuenta

## Resumen

Interruptor que controla si una cuenta de usuario puede iniciar sesión en el sistema. Es una suspensión, no un borrado: la cuenta sigue existiendo en la base de datos y todo su histórico asociado (ventas, recetas, movimientos de inventario) permanece intacto. Solo el administrador puede activar o desactivar cuentas.

## Comportamiento

El estado activo/inactivo de la cuenta se representa con la columna `bActiva` (booleano) en la tabla `users`. El toggle se ejecuta desde el listado de usuarios o desde la vista de detalle, mediante un control de un solo clic.

### Login con cuenta inactiva

Cuando un usuario inactivo intenta iniciar sesión, el `AuthService.login` rechaza el intento aunque las credenciales sean correctas. Se devuelve a la vista de login con el mensaje "Cuenta suspendida. Contacte al administrador." En la práctica, después de validar credenciales contra la tabla `users`, se verifica `bActiva` antes de llamar a `Auth::login()`. Si está en `false`, no se establece sesión y se redirige con error.

### Sesiones activas en el momento de la desactivación

Las sesiones que ya están abiertas no se invalidan instantáneamente. La invalidación es perezosa: en el siguiente request del usuario, un middleware (o el propio `EnsureRole`) verifica que `auth()->user()->bActiva === true`. Si la cuenta fue desactivada mientras la sesión estaba activa, el middleware ejecuta `Auth::logout()`, invalida la sesión, y redirige al login con el mismo mensaje de cuenta suspendida.

Este es un trade-off explícito del MVP: no se mantiene un registro centralizado de sesiones activas para forzar el cierre inmediato. Para el caso de uso (administrador de farmacia) la latencia de "siguiente request" es aceptable.

### Reactivación

Reactivar una cuenta es simplemente volver a poner `bActiva = true`. El usuario recupera la capacidad de iniciar sesión en su próximo intento. Las sesiones que fueron cerradas durante el periodo de inactividad no se reabren automáticamente — el usuario debe hacer login normalmente.

### Histórico

La desactivación no toca ningún dato relacionado del usuario. Las ventas que registró siguen apareciendo en reportes con su nombre, las recetas que dispensó siguen vinculadas a su id, los movimientos de inventario que generó conservan la referencia. La suspensión es estrictamente sobre la capacidad de autenticarse.

## Restricciones

- Solo `administrator` puede invocar el toggle (middleware `role:administrator`).
- La ruta es `PATCH /usuarios/{user}/activa` y devuelve `RedirectResponse` al listado.
- El controlador es delgado: solo recibe el request, llama al servicio y redirige.
- Toda la lógica (verificar el estado actual, invertir el flag, persistir) vive en `UsuarioService::toggleActiva()`.
