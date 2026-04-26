# Listado de Usuarios

## What does this part of the system do?
Es la pantalla principal del panel de cuentas. Muestra en una sola tabla a todo el personal de la farmacia con la información que el administrador necesita para tomar decisiones rápidas: nombre completo, correo, rol asignado, si la cuenta está activa o suspendida y la fecha de su último acceso.

Desde esta misma vista se entra a cualquier acción sobre una cuenta concreta. El admin no tiene que abrir un detalle para empezar a trabajar; el listado funciona como su tablero de control.

La idea es que con un par de filtros y una búsqueda pueda encontrar a quien quiera en segundos, incluso cuando la nómina de la farmacia crezca.

## Who uses it?
Solamente el administrador.

## How does it work?
El admin entra al panel de usuarios y ve la tabla completa con todas las cuentas. Tiene un buscador que filtra por nombre o correo conforme escribe, y filtros adicionales para acotar por rol y por estado activo o inactivo. Cada fila muestra los datos clave y, al final, los accesos directos a editar la cuenta, cambiar su contraseña o alternar su estado. Si la lista crece más allá de lo que cabe cómodamente en pantalla, aparece paginación al pie. Cuando un filtro deja la tabla vacía, la vista lo dice con un mensaje claro en lugar de mostrar una tabla en blanco.

## Out of scope
No hay exportación a CSV en el MVP, ni auditoría dentro de este panel sobre quién editó a quién: ese rastro lo cubre la bitácora general del sistema.

## Skills relevantes

- `/laravel-specialist` — para construir la query Eloquent con filtros combinables y paginación eficiente.
- `/tailwind-css-patterns` — para una tabla responsiva con badges legibles de rol y estado.
