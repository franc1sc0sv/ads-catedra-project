# Shape — Listado de Pedidos

## Decisiones

- Filtros combinables en una misma query (estado + proveedor + rango de fechas). Se construyen condicionalmente en el servicio; ausencia de un filtro = sin restricción.
- La máquina de estados vive en `PedidoService`, **no** en el controlador. El controlador solo traduce request a llamada de servicio y la respuesta a redirect/flash.
- `cancel` solo es válido desde `SOLICITADO`. Cualquier otra transición arroja excepción de dominio.
- `markEnviado` solo es válido desde `SOLICITADO`.
- Cancelar **no toca stock**. El stock se mueve únicamente al recibir un pedido (otra spec).
- Cancelar requiere `motivo` (validado en `CancelPedidoRequest`); se persiste en el pedido junto con timestamp de cancelación.
- Detalle de pedido recibido muestra ambas cantidades (solicitada y recibida) lado a lado.
- Listado eager-load `proveedor`; detalle eager-load `proveedor` + `lineas.producto` para evitar N+1.

## Riesgos / bordes

- Acciones concurrentes (dos usuarios cancelando o enviando el mismo pedido): proteger con verificación de estado dentro de la transacción del servicio.
- Filtro de fecha: usar rango inclusivo `[desde, hasta]` sobre `fecha_pedido`; validar `desde <= hasta`.
- Pagination preserve filters en query string (no perderlos al cambiar de página).

## Open questions

- ¿Tamaño de página default? (asumir 20 salvo indicación)
- ¿Ordenamiento default? (asumir `fecha_pedido desc`)
