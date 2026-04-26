# Logout

## What does this part of the system do?
Logout es la salida limpia del sistema. Resuelve algo tan importante como el login: asegurar que cuando alguien termina su jornada, deja de tener acceso. En una farmacia donde varias personas pueden compartir una computadora a lo largo del día, cerrar sesión correctamente es la diferencia entre que el siguiente turno empiece con su propia identidad o que use la del anterior por descuido.

La idea es que cerrar sesión sea siempre un gesto consciente y de un solo clic, sin pasos intermedios que confundan.

## Who uses it?
Todos los roles del personal: Administrador, Cajero, Farmacéutico y Encargado de Inventario.

## How does it work?
El usuario presiona el botón "Cerrar sesión" desde cualquier pantalla del sistema. La sesión asociada al navegador se destruye, la cookie deja de ser válida y se le devuelve a la pantalla de login. A partir de ese momento, intentar volver a una ruta interna lo redirige otra vez al login, porque ya no hay sesión activa. No hay pasos intermedios ni confirmaciones: un clic, sesión cerrada, y de vuelta al inicio.

## Skills relevantes

- `/laravel-specialist` — para el endpoint de logout y la destrucción de la sesión
- `/security-review` — para confirmar que la sesión queda completamente limpia y que no quedan rastros reutilizables en el navegador
