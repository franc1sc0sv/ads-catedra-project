# Catálogo de Proveedores

## Resumen

Mantiene el registro de empresas que abastecen a la farmacia. Alimenta el selector de proveedor en el módulo de pedidos y se relaciona con medicamentos. El encargado de inventario gestiona el catálogo; el administrador también tiene acceso completo.

## Alcance

### Modelo `Proveedor`

Campos:

- `cEmpresa` — nombre o razón social de la empresa proveedora.
- `cRfc` — identificador fiscal único. Inmutable una vez creado el registro.
- `cTelefono` — teléfono de contacto.
- `cCorreo` — correo electrónico de contacto.
- `cDireccion` — dirección física.
- `bActivo` — bandera booleana que controla la visibilidad como soft-delete lógico.

### Reglas de negocio

- **Búsqueda y listado**: la tabla muestra todos los proveedores, activos e inactivos. Se puede filtrar por nombre de empresa o por RFC.
- **Alta**: requiere los seis campos. `cRfc` valida unicidad.
- **Edición**: cualquier campo es editable excepto `cRfc`. Si el RFC cambió en la realidad, se da de baja el registro anterior y se crea uno nuevo.
- **Toggle activar/desactivar**: cambia `bActivo`. No borra el registro.
- **Borrado físico**: bloqueado si el proveedor tiene pedidos o medicamentos asociados. Solo se permite borrar registros sin dependencias.
- **Visibilidad en pedidos**: el selector de proveedor al crear un pedido solo muestra registros con `bActivo = true`. El historial de pedidos sigue mostrando proveedores inactivos correctamente.

### Roles

- `inventory_manager` — gestiona el catálogo (alta, edición, toggle, borrado).
- `administrator` — mismo acceso que `inventory_manager`.

## Fuera de alcance

- Importación masiva de proveedores.
- Auditoría de cambios sobre proveedores.
- Calificación o ranking de proveedores.
