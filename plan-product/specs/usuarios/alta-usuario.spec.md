# Alta de Usuario

## What does this part of the system do?
Es el formulario que usa el administrador para crear una cuenta nueva del personal. Captura los datos mínimos para que la persona pueda entrar al sistema desde el primer día: nombre completo, correo, rol y una contraseña inicial.

La cuenta nace lista para usarse. Una vez guardada, esa persona ya puede iniciar sesión con las credenciales que el admin le compartió en privado.

El formulario está pensado para ser rápido: pocos campos, validaciones inmediatas y un botón de guardar que devuelve al admin al listado con la cuenta nueva ya visible.

## Who uses it?
Solamente el administrador.

## How does it work?
El admin abre el formulario, llena los cuatro campos y envía. El sistema verifica que el correo no esté ya registrado en otra cuenta y que la contraseña tenga al menos la longitud mínima exigida; si algo falla, se muestra el error junto al campo correspondiente sin perder lo que ya estaba escrito. Cuando todo es válido, la cuenta se guarda como activa, la contraseña se almacena solamente como huella criptográfica y nunca en texto plano, y el admin regresa al listado con un mensaje de confirmación. A partir de ese momento, la persona puede iniciar sesión inmediatamente.

## Out of scope
No hay invitación por correo, ni generación automática de contraseñas seguras (la elige el admin), ni importación masiva en el MVP.

## Skills relevantes

- `/laravel-specialist` — para el form request con validación de unicidad y la creación del modelo.
- `/security-review` — para confirmar que el costo del hash y la política mínima de contraseña son razonables.
