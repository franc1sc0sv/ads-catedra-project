# Cancelar venta

## What does this part of the system do?

Cubre dos escenarios de cancelación muy distintos. La cancelación de una venta EN_PROCESO es trivial: como nunca se descontó stock ni se cobró, basta con marcarla cancelada. La cancelación de una venta COMPLETADA es la operación más sensible del sistema: implica reversa de cobro en Stripe cuando aplica, devolución al stock y un rastro auditable de quién canceló y por qué.

El histórico nunca se borra: la venta cancelada queda visible en reportes y en el historial del cliente, con su comprobante anulado pero accesible.

## Who uses it?

El cajero puede cancelar ventas EN_PROCESO; solo el administrador puede cancelar ventas COMPLETADAS, y siempre con motivo capturado.

## How does it work?

Una venta EN_PROCESO se cancela con un clic desde el mismo POS, sin captura de motivo, porque no afectó nada todavía.

Una venta COMPLETADA se cancela desde el módulo del administrador, que selecciona la venta y captura un motivo obligatorio. El sistema arranca una transacción que, si la venta tuvo cobro con tarjeta, primero invoca un refund a través de la API de Stripe; el identificador de refund que Stripe devuelve se guarda en el registro de cobro junto con la fecha de reembolso y el estado pasa a REEMBOLSADA. Si el refund falla, la cancelación se aborta completa, la venta sigue COMPLETADA y el administrador ve el error para resolverlo —no se devuelve stock ni se marca cancelada hasta que el problema con Stripe se resuelva.

Para ventas pagadas con efectivo o transferencia, la cancelación no toca Stripe; solo se generan los movimientos DEVOLUCION para devolver al stock línea por línea, y la venta pasa a CANCELADA conservando todos los datos originales.

El comprobante original queda anulado pero visible en el historial del cliente y en los reportes. Si la venta tenía receta vinculada, la receta sigue existiendo pero deja de estar atada a una venta válida. La cancelación es siempre total: no se puede cancelar solo una línea.

## Skills relevantes

- `/laravel-specialist` — para la transacción de reversa que actualiza venta, stock y movimientos en bloque
- `/laravel-patterns` — para encapsular la reversa Stripe + reversa de stock en un servicio testeable
- `/security-review` — porque la cancelación de COMPLETADA es la operación más expuesta a fraude interno y necesita auditoría estricta
