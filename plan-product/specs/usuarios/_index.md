# Gestión de Usuarios

Esta sección es el panel del administrador para manejar las cuentas del personal de la farmacia. Aquí entra cuando necesita dar de alta a alguien nuevo, corregir los datos de una cuenta existente, cambiar el rol de una persona o suspender el acceso de quien ya no debería tenerlo.

También vive aquí la pantalla genérica de cambio de contraseña, que cualquier usuario autenticado usa para actualizar la suya. El propósito de fondo es tener un solo lugar donde se decide quién puede entrar al sistema y con qué nivel de acceso.

## What's inside this section

La sección está dividida entre el panel administrativo de cuentas y la pantalla de auto-servicio de contraseña. Lo administrativo cubre ver, crear, editar y suspender cuentas; el auto-servicio sirve para que cualquiera mantenga su propia clave al día.

- **listado-usuarios** — tabla con todas las cuentas del personal, con filtros por rol y estado y búsqueda por nombre o correo.
- **alta-usuario** — formulario para crear una cuenta nueva con nombre, correo, rol y contraseña inicial.
- **edicion-rol** — pantalla para corregir los datos de una cuenta existente, incluyendo el rol asignado.
- **activar-desactivar** — interruptor para suspender o reactivar el acceso de una cuenta sin borrar su histórico.
- **cambiar-password** — pantalla compartida para que cada usuario actualice su contraseña, o para que el admin resetee la de otro.

## What data does this section work with?

Trabaja casi en exclusiva con la entidad Usuario: nombre, correo, rol asignado, estado activo y la huella de su contraseña. El rol que se guarda aquí es el que después determina a qué áreas del sistema puede entrar la persona.

## What does this section depend on?

Depende de la sección de Autenticación y Roles, ya que esta sección administra precisamente las cuentas que aquella autentica.

## Skills relevantes

- `/laravel-specialist` — para los controladores CRUD de usuarios y el guard de admin que protege todo el panel.
- `/tailwind-css-patterns` — para las tablas y formularios del panel, que deben verse limpios y ser cómodos de usar.
- `/security-review` — antes de mergear cualquier cambio que toque hashing de contraseñas o cambio de rol, para no introducir regresiones de seguridad.
