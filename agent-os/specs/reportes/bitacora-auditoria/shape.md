# Shape — Bitácora de Auditoría

## Forma del problema

Una **tabla apéndice** que muchos controladores escriben y un solo controlador lee. La tensión clave: dispersar `log()` en muchos call-sites sin acoplar la lógica de auditoría a cada controlador.

## Decisiones de shaping

- **Escritura distribuida vía interface inyectada.** Cada controlador relevante recibe `BitacoraServiceInterface` por constructor (readonly promotion). Los controladores no construyen filas ni saben de tabla; llaman `log()` con argumentos de dominio. Toda la lógica de serialización de `cDetalles` (IP, User-Agent, before/after) vive en `BitacoraService`.
- **Lectura centralizada.** Un único `BitacoraController@index` consulta. Filtros y paginación quedan en el servicio (`getFiltered()`), no en el controlador.
- **Inmutabilidad por ausencia.** No hay rutas `update`/`destroy`. No hay método `update`/`delete` en el servicio. La tabla solo crece. Sin triggers de DB en el MVP — confiamos en que no existan endpoints.
- **`LOGIN_FAIL` con `cveUsuario` null.** Cuando el email intentado no corresponde a un usuario, `cveUsuario` queda null y el email intentado va dentro de `cDetalles` JSON (campo `email_intentado`). Esto evita FK rota y conserva la evidencia.
- **Sin retención en MVP.** No se purgan registros. No hay job de limpieza. Si la tabla crece demasiado, se aborda fuera del MVP.
- **Default 24h.** El index sin filtros muestra solo las últimas 24 horas para que la consulta inicial sea barata; filtros explícitos pueden ampliar el rango.
- **Solo administrador.** `role:administrator` middleware corta antes de tocar la query. Otros roles ven 403, no lista vacía.

## Lo que no entra

- Exportación CSV/PDF de la bitácora.
- Alertas en tiempo real ante eventos críticos.
- Diff visual de estado anterior vs nuevo (se guarda en JSON, pero la vista lo muestra plano).
- Retención / archivado / particionado.
