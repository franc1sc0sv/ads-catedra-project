# Login Web

## What does this part of the system do?
Esta es la pantalla por la que entra al sistema cualquier persona del personal de la farmacia desde una computadora con navegador. Resuelve el problema más básico: confirmar que quien dice ser X realmente es X, y dejarlo dentro del sistema sin que tenga que volver a identificarse en cada pantalla mientras dure su jornada.

Una vez adentro, el usuario no aterriza en una página genérica: el sistema lo lleva directamente al dashboard que corresponde a su rol, porque lo que ve el administrador no tiene nada que ver con lo que ve el cajero. Esa decisión se toma aquí, en el momento del login.

La sección está pensada para ser cotidiana y rápida. La gente abre el sistema varias veces al día, y la fricción se nota.

## Who uses it?
Todos los roles del personal: Administrador, Cajero, Farmacéutico y Encargado de Inventario.

## How does it work?
El usuario llega a la pantalla de login y ve un formulario con dos campos: correo y contraseña. Los completa y envía. Si las credenciales son correctas y la cuenta está activa, el sistema crea una sesión segura asociada al navegador y lo redirige al dashboard de su rol —cada rol tiene un destino distinto y eso se decide en este paso. A partir de ahí puede moverse por el sistema sin volver a ingresar credenciales hasta que cierre sesión o expire la cookie. Si el correo no existe, la contraseña no coincide, o la cuenta está marcada como inactiva, ve un único mensaje genérico que no revela cuál de las tres cosas falló: esto es deliberado, para no filtrar información sobre qué correos están registrados. Si alguien insiste con intentos fallidos repetidos, una versión futura podría agregar un bloqueo temporal, pero eso queda fuera del alcance inicial.

## Skills relevantes

- `/laravel-specialist` — para el guard web por sesión y el redireccionamiento por rol al dashboard correspondiente
- `/tailwind-css-patterns` — para el formulario de login responsivo y limpio
- `/accessibility` — para que el formulario sea utilizable con teclado y lectores de pantalla
