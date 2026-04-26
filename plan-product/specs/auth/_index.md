# Autenticación y Roles

Esta sección es la puerta de entrada a FarmaSys. Cualquier persona del personal de la farmacia (administrador, cajero, farmacéutico o encargado de inventario) entra por aquí: se identifica con su correo y contraseña, y a partir de ese momento el sistema sabe quién es y qué tiene permitido hacer.

El problema que resuelve es simple pero crítico: asegurar que solo gente autorizada use el sistema, y que cada quien vea únicamente lo que le corresponde según su rol. La identificación es por sesión del navegador, sin tokens externos ni stacks paralelos.

## What's inside this section

La autenticación se divide en tres partes que cubren el ciclo completo de identidad del usuario. Login web cubre la entrada por el navegador con cookie de sesión. Logout cubre la salida limpia. Y middleware de roles es la pieza transversal que en cada ruta decide si la persona autenticada tiene permiso para estar ahí.

- **login-web** — el usuario entra desde el navegador con su correo y contraseña y queda con una sesión activa que lo lleva al dashboard de su rol.
- **logout** — cierra la sesión destruyendo la cookie del navegador y devuelve al usuario a la pantalla de login.
- **middleware-roles** — en cada ruta del sistema decide si el rol del usuario autenticado tiene permiso para acceder.

## What data does this section work with?

Trabaja con la entidad **Usuario**: correo, contraseña almacenada de forma segura, rol asignado y estado activo/inactivo. Toda la sección gira alrededor de validar credenciales contra ese registro y de leer el rol para tomar decisiones de acceso.

## What does this section depend on?

Ninguna dependencia — esta sección se construye primero porque todas las demás (ventas, inventario, recetas, reportes) dependen de saber quién es el usuario y qué rol tiene.

## Skills relevantes

- `/laravel-specialist` — para el guard de sesión, middleware de roles y controladores web
- `/security-review` — antes de mergear cualquier cambio que toque credenciales o el manejo de sesión
- `/php-pro` — para tipado estricto y DTOs en los servicios de autenticación
