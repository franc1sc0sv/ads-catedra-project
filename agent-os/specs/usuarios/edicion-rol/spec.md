# Edición de Cuenta y Rol

## Resumen

Permitir al administrador editar una cuenta de usuario existente: nombre, correo y rol. El cambio de rol redefine los accesos del usuario afectado en el siguiente request que realice. La pantalla de edición muestra el rol actual y permite seleccionar el nuevo rol.

## Alcance

- Solo el rol `administrator` puede acceder a esta funcionalidad.
- Campos editables: `nombre`, `correo`, `role`.
- El correo debe ser único en la tabla `users`, excluyendo el id del usuario que se está editando (regla `unique:users,email,{id}`).
- El rol se valida contra el enum `App\Enums\UserRole`.
- Los cambios se guardan al instante (no hay borrador).
- No se fuerza logout del usuario afectado: el middleware `EnsureRole` lee `auth()->user()->role->value` en cada request, así que el nuevo rol toma efecto en el siguiente request.
- Si el usuario afectado intenta acceder a una ruta que ya no le corresponde, `EnsureRole` lo redirige a su dashboard correcto según el nuevo rol.

## Reglas de negocio

- El administrador no puede degradarse a sí mismo dejando al sistema sin administradores (validación opcional fuera de MVP, documentar como futura mejora si aplica).
- Cualquier cambio de rol queda registrado en bitácora con acción `ROL_CAMBIADO`, incluyendo `role_anterior` y `role_nuevo`. El registro se hace desde el servicio, inyectando `BitacoraServiceInterface`.
- Cambios de nombre o correo no generan entrada de bitácora en este MVP (solo el cambio de rol es auditable por seguridad).

## Flujo

1. Administrador abre listado de usuarios y elige uno → `GET /admin/usuarios/{usuario}/edit`.
2. Vista `admin/usuarios/edit.blade.php` muestra formulario con valores actuales y un select de rol que indica claramente el rol actual y el nuevo seleccionable.
3. Submit → `PUT /admin/usuarios/{usuario}` → `UpdateUsuarioRequest` valida → `UsuarioController::update` llama a `UsuarioServiceInterface::update`.
4. El servicio actualiza el modelo. Si el rol cambió, registra `ROL_CAMBIADO` en bitácora.
5. Retorna `RedirectResponse` al listado con mensaje flash de éxito.

## Fuera de alcance

- Cambio de contraseña (otra historia).
- Eliminación de usuarios (otra historia).
- Notificación al usuario afectado por correo.
