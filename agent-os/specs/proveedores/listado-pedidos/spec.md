# Listado de Pedidos

## Resumen

Vista central para consultar pedidos a proveedores con filtros combinables por estado, proveedor y rango de fechas. Punto de entrada para cancelar un pedido en estado `SOLICITADO` o marcarlo como `ENVIADO`. El encargado de inventario opera; el administrador solo lee.

## Alcance

- Listado paginado de pedidos con columnas: número, proveedor, fecha, total, estado, usuario creador.
- Filtros combinables (estado, proveedor, fecha desde, fecha hasta) aplicados a la misma query.
- Vista detalle muestra cabecera y líneas; si el pedido fue recibido, muestra cantidad solicitada vs recibida por línea.
- Acciones state-gated desde detalle de pedido `SOLICITADO`: cancelar (motivo obligatorio) o marcar `ENVIADO`.
- No se permite cancelar `ENVIADO` (la única salida de `ENVIADO` es recibir, eventualmente con cantidades en cero).
- No se permite cancelar `RECIBIDO`.

## Comportamiento

- Listado eager-load de proveedor para evitar N+1.
- Detalle eager-load de proveedor + líneas (con producto).
- Filtros se construyen en el servicio a partir de un DTO/array de criterios; controller solo pasa input validado.
- Transiciones de estado viven en `PedidoService`: `cancel(Pedido $pedido, string $motivo)` y `markEnviado(Pedido $pedido)`. Ambas validan estado actual y arrojan excepción de dominio si la transición no es válida.
- Cancelar registra `motivo_cancelacion` y timestamp; no toca stock (el stock solo se mueve al recibir, en otra spec).
- Marcar enviado solo cambia estado y timestamp; no toca stock.

## Roles

- `inventory_manager`: lectura + acciones (cancelar, marcar enviado).
- `administrator`: solo lectura del listado y detalle.

## No objetivos

- Crear pedido (otra spec).
- Recibir pedido y mover stock (otra spec).
- Edición de líneas tras crear el pedido.
