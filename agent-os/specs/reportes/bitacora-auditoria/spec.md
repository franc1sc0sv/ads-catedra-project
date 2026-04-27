# Bitácora de Auditoría — Spec

## Propósito

Bitácora inmutable consultable solo por el rol `administrator`. Registra eventos de seguridad y operación: `LOGIN_OK`, `LOGIN_FAIL`, `LOGOUT`, `RECETA_VALIDADA`, `RECETA_RECHAZADA`, `AJUSTE_STOCK`, `VENTA_CANCELADA`, `USUARIO_CREADO`, `ROL_CAMBIADO`.

## Modelo de datos

`AuditoriaAcceso` (tabla append-only):

- `cveAuditoria` — PK auto-increment
- `cveUsuario` — FK a `usuarios`, **nullable** (null en `LOGIN_FAIL` cuando el email no existe)
- `cAccion` — string corto del enum de acciones
- `cTablaAfectada` — string nullable (e.g. `"recetas"`, `"ventas"`, `"usuarios"`, `"productos"`)
- `cRegistroAfectado` — string nullable (e.g. `"cveVenta=1234"`)
- `cDetalles` — JSON: estado anterior, estado nuevo, IP, navegador (User-Agent), observación, email intentado en `LOGIN_FAIL`
- `fCreado` — timestamp de creación

No hay `fActualizado` ni columnas de soft-delete: la tabla es estrictamente append-only.

## Escritura (logging)

Las escrituras ocurren desde múltiples controladores vía un servicio inyectado:

- `AuthController` → `LOGIN_OK`, `LOGIN_FAIL`, `LOGOUT`
- `RecetaController` → `RECETA_VALIDADA`, `RECETA_RECHAZADA`
- `StockController` → `AJUSTE_STOCK`
- `VentaController` → `VENTA_CANCELADA`
- `UserController` → `USUARIO_CREADO`, `ROL_CAMBIADO`

Cada controlador inyecta `BitacoraServiceInterface` y llama `log(...)` en el punto de la acción. La lógica de qué guardar y cómo serializar `cDetalles` vive en `BitacoraService`, no en los controladores.

## Lectura

Solo el `BitacoraController@index` lee la bitácora, protegido por `auth` + `role:administrator` (otros roles reciben 403 antes de cargar datos).

- Default: últimas 24 horas, ordenadas por `fCreado` desc.
- Filtros: `cveUsuario`, `cAccion`, `cTablaAfectada`, rango `fDesde`/`fHasta`.
- Paginación del lado del servidor.

## Inmutabilidad

No existen rutas `update` ni `destroy` para `AuditoriaAcceso`. La inmutabilidad se garantiza por ausencia de endpoints, no por triggers de DB en el MVP.

## Vistas

`resources/views/admin/reportes/bitacora.blade.php` — tabla paginada con filtros.
