# Catálogo de Proveedores

## What does this part of the system do?

Mantiene el registro de las empresas que abastecen a la farmacia. Desde aquí se dan de alta proveedores nuevos, se corrigen sus datos y se desactivan los que ya no operan. Es el catálogo que alimenta el selector de proveedor al crear un pedido.

## Who uses it?

El encargado de inventario gestiona el catálogo. El administrador también tiene acceso.

## How does it work?

La pantalla principal muestra una tabla con todos los proveedores —activos e inactivos— con búsqueda por nombre o RFC. Cada fila expone empresa, teléfono, correo, RFC y estado activo/inactivo.

**Alta:** El encargado abre el formulario de nuevo proveedor y captura empresa, RFC, teléfono, correo y dirección. El RFC es único en todo el sistema; intentar registrar uno duplicado devuelve error de validación antes de persistir. El proveedor nace activo por defecto.

**Edición:** Desde la fila o el detalle del proveedor se abre el mismo formulario con los datos precargados. Se puede modificar cualquier campo excepto el RFC —el RFC identifica fiscalmente al proveedor y no debe cambiar; si cambió, se da de baja el registro anterior y se crea uno nuevo. Al guardar, `fActualizado` se regenera.

**Desactivar / Reactivar:** Un toggle en la fila cambia el estado. Desactivar un proveedor no borra nada: los pedidos históricos y los medicamentos asociados quedan intactos. Un proveedor inactivo no aparece en el selector al crear nuevos pedidos, pero sí en el historial de pedidos existentes. Reactivar lo vuelve seleccionable sin más pasos.

No se permite borrar físicamente un proveedor si tiene pedidos o medicamentos asociados —el borrado lógico mediante `bActivo` es la única operación de "eliminación".

## Out of scope

Manejo de múltiples contactos por proveedor, integración con catálogos externos de proveedores, y carga masiva por CSV.

## Skills relevantes

- `/laravel-specialist` — para el CRUD de Proveedor con la regla de unicidad de RFC en validaciones y migración
- `/tailwind-css-patterns` — para el formulario de alta/edición y la tabla con estado activo/inactivo visible
