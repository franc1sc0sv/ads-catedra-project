# Ajuste de Stock — Spec

## Resumen

Ajuste de Stock es la única vía oficial para corregir el stock de un medicamento fuera del flujo de ventas y pedidos. Cuando lo que dice el sistema no coincide con la realidad física del inventario, el encargado de inventario aplica una corrección registrada como un evento auditable.

## Actores

Solo el rol `inventory_manager` accede a esta función. Ningún otro rol puede crear ni ver ajustes desde este flujo.

## Flujo principal

El encargado de inventario:

1. Busca el medicamento a ajustar.
2. Elige el tipo de ajuste:
   - `ajuste_manual` — corrección manual de discrepancia entre sistema y realidad.
   - `baja_vencimiento` — retiro de stock por vencimiento.
   - `devolucion` — devolución que reincorpora stock.
3. Indica la cantidad (positiva o negativa).
4. Escribe un motivo obligatorio en texto libre.
5. Confirma.

## Transacción atómica

Al confirmar, dentro de una sola transacción de base de datos:

- Se lee el stock actual del medicamento (`stock_antes`).
- Se calcula el nuevo stock aplicando la cantidad firmada (`stock_despues`).
- Se actualiza el stock del medicamento.
- Se crea un registro `MovimientoInventario` con: tipo, cantidad, stock_antes, stock_despues, motivo, fecha, usuario, medicamento.

Si cualquier paso falla, la transacción se revierte completa. No se permite que el stock cambie sin un movimiento asociado, ni un movimiento sin que el stock cambie.

## Inmutabilidad

El registro `MovimientoInventario` es inmutable: no hay edición ni eliminación. Si el encargado se equivocó en un ajuste, aplica un nuevo ajuste compensatorio en sentido contrario. Esto preserva la huella auditable completa.

## Validaciones

- `motivo` requerido, texto no vacío.
- `tipo` debe ser uno de los tres valores del enum (`ajuste_manual`, `baja_vencimiento`, `devolucion`).
- `cantidad` no puede ser cero.
- El stock resultante no puede ser negativo (regla de dominio enforced en el servicio dentro de la transacción; no permitida una baja mayor al stock disponible).

## Fuera de alcance

- Ajustes masivos por archivo CSV o por múltiples medicamentos en una sola operación.
- Edición o eliminación de movimientos existentes.
- Notificaciones automáticas a otros roles tras un ajuste.
- Workflow de aprobación de doble persona.
- Vinculación con órdenes de compra o ventas (esos flujos generan sus propios movimientos en otras specs).
- Visualización del historial de movimientos (spec separada).
