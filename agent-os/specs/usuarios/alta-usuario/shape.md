# Shape Notes - Alta de Usuario

## Decisiones clave

- **Hashing via cast, no manual.** El modelo `User` declara `'password' => 'hashed'` en `casts()`. La capa de servicio asigna la contrasena en texto plano y Eloquent la hashea al persistir. Evita doble hasheo y centraliza la regla.
- **Validacion en FormRequest, no en controller.** `CreateUsuarioRequest` concentra autorizacion (`role:administrator`) y reglas. El controller queda thin: `service->create($request->validated())`.
- **Email unico via DB constraint + rule.** La regla `unique:users,email` da el feedback al usuario; la columna `users.email` ya tiene unique index como red de seguridad.
- **Rol como enum, validado.** `Rule::enum(UserRole::class)` rechaza cualquier valor fuera de los cuatro casos. El modelo tiene `'role' => UserRole::class` en `casts()`, asi que se persiste como enum.
- **Activa por defecto.** No hay columna `active` en el MVP; existir en la tabla `users` ya implica que la cuenta puede iniciar sesion. Si mas adelante se introduce soft-disable, sera otro spec.
- **Create-and-redirect.** Patron PRG estandar: `store` redirige al listado (`admin.usuarios.index`) con `with('status', '...')`. La pantalla de listado muestra el flash.
- **Errores junto al campo.** La vista usa `@error('campo')` por input y reinyecta `old('campo')` para no perder lo escrito.

## Riesgos / pendientes

- Listado de usuarios (`admin.usuarios.index`) es spec hermano; el redirect lo asume existente. Si aun no esta, se ajusta el target temporalmente al dashboard admin.
- Confirmacion de contrasena (`password_confirmation`) anade un campo extra a la UI; aceptado para evitar typos en alta manual.
- Logging/auditoria de creacion queda fuera de este spec.
