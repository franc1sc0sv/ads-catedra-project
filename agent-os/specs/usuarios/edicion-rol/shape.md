# Shape — Edición de Cuenta y Rol

## Decisiones de diseño

### Propagación lazy del rol vía middleware
No hay token de sesión que invalidar ni evento que disparar al cambiar el rol. `EnsureRole` lee `auth()->user()->role->value` en cada request, por lo que el cambio se aplica de forma natural al siguiente hit. Esto evita complejidad innecesaria (no hay que guardar versión del rol, no hay que firmar tokens, no hay que cerrar sesiones activas).

Implicación: si el usuario afectado está navegando una sección que ahora no le corresponde, su próximo click lo redirige al dashboard correcto. No se le notifica explícitamente del cambio de rol.

### Sin force-logout
Se evaluó forzar logout del usuario afectado (vía `Auth::logoutOtherDevices` o invalidando su sesión en la tabla `sessions`). Se descarta porque:
- Agrega coupling con el almacenamiento de sesiones.
- El comportamiento por middleware ya es seguro: no puede acceder a rutas protegidas con su rol viejo.
- Mantiene la UX más fluida (no kick out abrupto).

### Auditoría solo en cambio de rol
El cambio de rol es información sensible (eleva o reduce privilegios). Cambios de nombre o correo no se auditan en MVP para mantener la bitácora enfocada en eventos de seguridad. Esto se puede expandir más adelante.

El log se hace desde el servicio, no desde el controller, para mantener la lógica de negocio centralizada y testeable. `BitacoraServiceInterface` se inyecta en el constructor del `UsuarioService`.

### Email único excepto self
Regla `unique:users,email,{id}` para evitar falso positivo cuando el admin guarda el formulario sin cambiar el correo. Se obtiene el id del modelo via `$this->route('usuario')->id` en `UpdateUsuarioRequest::rules()`.

### Validación de rol contra enum
Se usa `Rule::enum(UserRole::class)` para asegurar que solo se aceptan valores válidos del enum backed. Esto evita strings arbitrarios incluso si alguien manipula el form.

## Riesgos / no resueltos

- **Auto-degradación del último admin**: el admin podría cambiar su propio rol y dejar al sistema sin administradores. Fuera de alcance del MVP; se documenta como mejora futura (validación que cuente admins activos antes de degradar).
- **Concurrencia**: si dos admins editan al mismo usuario simultáneamente, gana el último. Aceptable para MVP.
