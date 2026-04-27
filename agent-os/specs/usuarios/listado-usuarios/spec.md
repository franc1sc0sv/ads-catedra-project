# Listado de Usuarios

## Resumen

Pantalla principal del panel de cuentas accesible solo para administradores. Presenta la lista completa del personal del sistema en una tabla paginada con capacidades de búsqueda y filtrado, junto con acciones rápidas por fila.

## Alcance funcional

### Tabla de usuarios

La tabla muestra una fila por usuario con las siguientes columnas:

- **Nombre** — nombre completo del usuario
- **Correo** — email registrado
- **Rol** — uno de `administrator`, `salesperson`, `inventory_manager`, `pharmacist` (mostrado con su `label()` legible)
- **Estado** — activo / inactivo
- **Último acceso** — fecha y hora del último login exitoso (`fUltimoAcceso`); muestra "Nunca" cuando es null

### Búsqueda

Un único campo de búsqueda libre que filtra por coincidencia parcial sobre nombre y correo simultáneamente (ILIKE en PostgreSQL).

### Filtros

Dos filtros combinables aplicables junto con la búsqueda:

- **Rol** — selector con todas las opciones del enum `UserRole` más "Todos"
- **Estado** — selector activo / inactivo / todos

Los filtros y la búsqueda se envían vía query string (`GET`) para que la URL sea compartible y la paginación los preserve.

### Acciones por fila

Cada fila expone tres enlaces directos (sin modal en MVP):

- **Editar** — navega a la pantalla de edición del usuario
- **Cambiar password** — navega a la pantalla de cambio de contraseña
- **Toggle estado** — submit POST que invierte el flag activo/inactivo

### Paginación

Paginación estándar de Laravel (`paginate(15)`), que conserva los parámetros de búsqueda y filtros en los enlaces (`->withQueryString()`).

### Estado vacío

Cuando los filtros aplicados no producen resultados, la tabla muestra un mensaje claro indicando que no hay coincidencias y sugiriendo limpiar filtros. Cuando no hay usuarios en absoluto en el sistema, el mensaje es distinto.

## Tracking de último acceso

La columna `fUltimoAcceso` se actualiza en el flujo de login exitoso dentro del `AuthController` (no vía cron, no vía evento listener separado en MVP). Se persiste con `now()` tras autenticar al usuario.

## Restricciones

- Solo accesible para usuarios con rol `administrator` (middleware `role:administrator`)
- No incluye exportación CSV (excluido del MVP — la bitácora general lo cubre)
- No incluye log de auditoría dentro del panel (excluido del MVP — bitácora general)

## Flujo

1. Administrador navega a `/admin/usuarios`
2. Controller delega en `UsuarioServiceInterface::list()` pasando filtros validados
3. Service retorna `LengthAwarePaginator<User>` aplicando search + filtros
4. View renderiza tabla con acciones por fila y paginador
