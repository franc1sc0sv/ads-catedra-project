# Cancelar venta

## What does this part of the system do?

Cubre dos escenarios de cancelación muy distintos. La cancelación de una venta EN_PROCESO es trivial: como nunca se descontó stock ni se cobró, basta con marcarla cancelada. La cancelación de una venta COMPLETADA es la operación más sensible del sistema: implica devolución al stock y un rastro auditable de quién canceló y por qué.

El histórico nunca se borra: la venta cancelada queda visible en reportes y en el historial del cliente, con su comprobante anulado pero accesible.

## Who uses it?

El cajero puede cancelar ventas EN_PROCESO; solo el administrador puede cancelar ventas COMPLETADAS, y siempre con motivo capturado.

## How does it work?

Una venta EN_PROCESO se cancela con un clic desde el mismo POS, sin captura de motivo, porque no afectó stock ni cobro todavía.

Una venta COMPLETADA se cancela desde el módulo del administrador, que selecciona la venta y captura un motivo obligatorio. El sistema arranca una transacción que genera un movimiento DEVOLUCION por cada línea de la venta, devolviendo las unidades al stock, y marca la venta como CANCELADA. Los movimientos DEVOLUCION generados por cancelación de venta llevan `cveVenta` poblado en `MovimientoInventario`, lo que los distingue de las devoluciones manuales desde ajuste de stock, donde `cveVenta` es NULL.

El comprobante original queda anulado pero visible en el historial del cliente y en los reportes. Si la venta tenía recetas vinculadas, las filas en `VentaReceta` se conservan como referencia histórica; las recetas en estado VALIDADA quedan disponibles para re-vincularse a otra venta activa. La cancelación es siempre total: no se puede cancelar solo una línea.

## Skills relevantes

- `/laravel-specialist` — para la transacción de reversa que actualiza venta, stock y movimientos en bloque
- `/laravel-patterns` — para encapsular la reversa de stock en un servicio testeable
- `/security-review` — porque la cancelación de COMPLETADA es la operación más expuesta a fraude interno y necesita auditoría estricta
