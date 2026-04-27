# Shape: Recibir Pedido

## Decisiones de diseño

### All-or-nothing en una transacción
Toda la recepción ocurre dentro de `DB::transaction()`. Si la suma de stock falla en la línea N, el cambio de estado del pedido y los movimientos de las líneas 1..N-1 también se revierten. No existen estados intermedios "a medias" — el pedido o queda `RECIBIDO` con todos sus efectos, o sigue `SOLICITADO`/`ENVIADO` sin tocar nada.

### Entrega parcial es un caso normal
`nCantidadRecibida` puede ser menor que `nCantidadSolicitada` y eso no requiere un estado especial (`PARCIAL`, `INCOMPLETO`, etc). El pedido pasa a `RECIBIDO`; lo que falta queda implícito en la diferencia entre las dos columnas. Esto evita una máquina de estados más rica a cambio de un reporte futuro "diferencias solicitado vs recibido" que cualquier select puede resolver.

### Recibir-con-cero como cierre administrativo
Para `ENVIADO` que nunca llega (proveedor incumplió, transportista lo perdió), el encargado captura `nCantidadRecibida = 0` en todas las líneas y cierra. El pedido queda `RECIBIDO` con el rastro auditable (`cveUsuarioReceptor`, `fRecepcion`) sin afectar stock ni crear movimientos engañosos. Alternativa rechazada: estado `CANCELADO` separado — agrega ramificación al modelo sin valor real para el MVP.

### Precio real vive en la línea, no en el pedido
`nPrecioReal` se almacena en `DetallePedido`. **No** se recalcula un `nTotalReal` agregado en `Pedido`. Razones:
- La fuente de verdad atómica por línea queda íntegra para auditoría y reportes de costos por medicamento.
- Cualquier total se deriva con `SUM(nCantidadRecibida * nPrecioReal)` en una query — no hay denormalización que mantener consistente.
- Cambios futuros de regla (descuentos, IVA, mermas) se acomodan sin migración del campo agregado.

### Movimiento de inventario con FK al pedido
`MovimientoInventario` de tipo `ENTRADA_COMPRA` lleva `cvePedido` apuntando al pedido origen. Permite responder "¿de qué pedido salió este stock?" desde reportes de inventario sin reconstruir join por fechas. El precio del movimiento se toma de `nPrecioReal` (fallback a `nPrecioEstimado`) para que el costeo de inventario refleje el costo real cuando se conoce.

## Lo que no entra en este spec

- Devoluciones a proveedor (un movimiento `SALIDA_DEVOLUCION` con FK al pedido) — feature aparte.
- Reportes de "diferencias solicitado vs recibido" — query, no feature.
- Notificaciones al solicitante del pedido — fuera del MVP de recepción.
