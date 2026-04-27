# Shape Notes — Catálogo de Proveedores

## Inmutabilidad del RFC

El RFC identifica fiscalmente al proveedor frente al SAT. Cambiarlo después de crear el registro rompería la trazabilidad de pedidos previos y de medicamentos asociados. Decisión:

- `cRfc` se valida `unique` y se acepta solo en alta.
- En `UpdateProveedorRequest` no existe la regla; el `ProveedorService::update()` descarta la clave `cRfc` del payload aunque venga.
- En la vista de edición el campo se renderiza como `readonly` con nota corta (“Si el RFC cambió, da de baja este proveedor y crea uno nuevo”).

## Soft-delete lógico vs físico

Se opta por una bandera `bActivo` en lugar de `SoftDeletes` de Laravel porque:

- La regla de negocio diferencia explícitamente “inactivo pero visible en historial” de “borrado”.
- El selector de pedidos solo necesita `where('bActivo', true)`, mientras que el historial de pedidos consulta vía la relación normal.
- El borrado físico solo se permite cuando no hay dependencias; eso requiere una verificación explícita, no un soft-delete genérico.

`Proveedor::scopeActivos()` queda disponible para el selector del módulo de pedidos. Otros listados (índice del catálogo, historial) consultan sin ese scope.

## Toggle vs Delete

Son dos operaciones distintas, con dos rutas y dos verbos HTTP:

- `PATCH /proveedores/{proveedor}/toggle` — alterna `bActivo`. Siempre permitido.
- `DELETE /proveedores/{proveedor}` — borrado físico. Bloqueado si hay pedidos o medicamentos asociados; en ese caso la acción redirige con error y sugiere usar el toggle.

La UI del listado muestra ambas acciones, pero deshabilita visualmente el borrado cuando hay dependencias (chequeo barato vía `relationLoaded` o conteo cacheado).

## Visibilidad en pedidos vs historial

- Crear pedido → autocomplete / select que consume `Proveedor::activos()->orderBy('cEmpresa')`.
- Ver historial / detalle de pedido → relación directa `pedido->proveedor`, sin filtro por `bActivo`. Se muestra el nombre tal cual; opcionalmente con badge “(inactivo)”.

## Vistas duplicadas por rol

Las dos rutas (`admin` e `inventory_manager`) comparten el mismo controlador y la misma lógica. Las vistas se duplican bajo `resources/views/admin/proveedores/` y `resources/views/inventory-manager/proveedores/` siguiendo el estándar `frontend/role-namespacing`. El controlador resuelve el path con `auth()->user()->role->value` y devuelve `view("{$rolePath}.proveedores.index", ...)`. La duplicación es deliberada por el estándar; si en el futuro las vistas divergen entre roles, queda espacio sin refactor.
