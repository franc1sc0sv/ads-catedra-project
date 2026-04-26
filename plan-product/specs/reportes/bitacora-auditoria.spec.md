# Bitácora de Auditoría

## What does this part of the system do?
Registra cada acción sensible del sistema en una bitácora inmutable que solo el administrador puede consultar. Es la herramienta para responder con evidencia preguntas como "¿quién canceló esa venta?", "¿quién cambió el rol de este usuario?" o "¿cuándo entró este cajero por última vez?".

Cada entrada captura el usuario responsable, la acción, la tabla y el registro afectados, los detalles relevantes, la dirección IP desde la que se hizo y el momento exacto. Las acciones rastreadas incluyen logins y logouts, alta/edición/baja de usuarios, ajustes manuales de stock, cancelaciones de venta, validación o rechazo de recetas y cambios de rol — todo lo que puede ocultar abuso o error operativo.

## Who uses it?
Exclusivamente el administrador. Ningún otro rol tiene acceso a la pantalla, ni siquiera de lectura, porque la bitácora misma contiene información sensible sobre el resto del personal.

## How does it work?
El admin entra a la bitácora y por defecto ve las entradas de las últimas 24 horas, ordenadas de más reciente a más antiguo. Filtra por usuario, acción, tabla afectada o rango de fechas según lo que esté investigando. Cada entrada incluye un código de acción (por ejemplo `LOGIN_OK`, `LOGIN_FAIL`, `LOGOUT`, `RECETA_VALIDADA`, `RECETA_RECHAZADA`, `AJUSTE_STOCK`, `VENTA_CANCELADA`, `USUARIO_CREADO`, `ROL_CAMBIADO`), la tabla afectada cuando aplica (por ejemplo `RegistroVentas`), el identificador del registro afectado en formato simple `clave=valor` (por ejemplo `cveVenta=1234`), y un campo de detalles que contiene un JSON con el contexto adicional: estado anterior, estado nuevo, IP de origen, navegador y observación si la hubo. Para los logins fallidos —correo inexistente o contraseña incorrecta— el campo de usuario va vacío porque no hay sesión que registrar, pero se conserva la dirección IP y el correo intentado dentro del campo de detalles, así el admin puede filtrar por `cAccion = LOGIN_FAIL` para detectar intentos sospechosos o ráfagas contra una misma cuenta. La paginación es del lado del servidor porque la bitácora crece rápido y no tiene retención automática en MVP. Los registros no se editan ni se borran — la bitácora es inmutable por diseño: el sistema solo escribe, nadie puede modificar entradas desde la interfaz, ni siquiera el propio administrador, así la bitácora preserva su valor probatorio. Si un usuario intenta entrar a la URL sin ser administrador, el sistema responde con 403 antes de cargar siquiera la primera fila.

## Skills relevantes

- `/laravel-specialist` — para la query con filtros combinados y la paginación eficiente sobre una tabla que crece rápido.
- `/security-review` — para confirmar que solo el administrador accede, que las entradas son inmutables desde la app y que los detalles no exponen secretos sensibles innecesariamente.
