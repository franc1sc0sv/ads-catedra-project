# Alta de Usuario

## Resumen

Permite al administrador crear una cuenta de usuario nueva en el sistema. La cuenta nace activa y la persona puede iniciar sesion inmediatamente con las credenciales asignadas. No existe flujo de invitacion por correo ni importacion masiva en el MVP.

## Actor

Solo `administrator`.

## Entradas

- Nombre completo (texto)
- Correo electronico (unico en `users.email`)
- Rol (uno de `App\Enums\UserRole`: `administrator`, `salesperson`, `inventory_manager`, `pharmacist`)
- Contrasena inicial (texto plano enviado una sola vez, longitud minima validada)

## Reglas

- El correo debe ser unico. Si ya existe, el formulario regresa con error junto al campo y conserva los demas valores escritos.
- La contrasena se guarda hasheada. El hashing se hace via el cast `'password' => 'hashed'` del modelo `User` (no se llama `Hash::make` manualmente al asignar).
- El rol enviado debe ser un valor valido del enum `UserRole`; cualquier otro valor falla validacion.
- La cuenta se persiste como activa por defecto. No hay paso intermedio de confirmacion.
- Errores de validacion se muestran junto al campo correspondiente sin perder el resto del input (`old()` en la vista).

## Salida

- Exito: redirect al listado de usuarios con mensaje flash de confirmacion.
- Error: regreso al formulario con errores y datos previos.

## Fuera de alcance (MVP)

- Envio de correo de bienvenida o invitacion.
- Generacion automatica de contrasena.
- Importacion masiva (CSV) de usuarios.
- Confirmacion de correo electronico.
