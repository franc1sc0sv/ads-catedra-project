# Middleware de Roles

## What does this part of the system do?
Esta es la pieza transversal que protege cada pantalla del sistema según el rol del usuario. Mientras el login se asegura de que la persona sea quien dice ser, el middleware de roles se asegura de que esa persona solo pueda entrar a las áreas que le corresponden.

El problema que resuelve es concreto: un cajero no debería poder abrir el panel de inventario, ni un farmacéutico debería poder ver reportes financieros del administrador. En vez de chequear esto manualmente en cada pantalla —donde tarde o temprano se olvida una— se centraliza en una sola regla que cada ruta declara una vez: "para entrar aquí necesitas tener este rol".

Es una pieza invisible para el usuario final cuando todo va bien, y muy visible cuando alguien intenta acceder a algo que no le corresponde: ahí responde con un mensaje claro de acceso denegado.

## Who uses it?
Es transversal a todos los roles. Cada ruta del sistema declara qué roles pueden pasar, y el middleware aplica la regla por igual a Administrador, Cajero, Farmacéutico y Encargado de Inventario.

## How does it work?
Cada ruta del sistema lleva escrita la lista de roles autorizados a entrar. Cuando un usuario hace una petición, el middleware revisa primero que esté autenticado, y luego compara el rol que tiene asignado con la lista permitida. Si coincide, la petición sigue su curso normal; si no, el sistema responde con un acceso denegado: una página clara que explica que no tiene permiso para estar ahí.

El rol se lee en cada petición a partir del usuario activo en la sesión, de forma que cualquier cambio que haga el administrador toma efecto en la siguiente acción del usuario afectado: no hay tokens vigentes que esperar a que expiren ni cachés intermedias. Si alguien acaba de pasar de cajero a farmacéutico, en su próxima carga de pantalla ya tiene los permisos nuevos.

## Skills relevantes

- `/laravel-specialist` — para el middleware de roles y su integración con el routing
- `/security-review` — para validar que ninguna ruta quede sin protección y que la decisión por rol sea consistente en todo el sistema
- `/php-pro` — para el enum tipado de roles usado en la verificación
