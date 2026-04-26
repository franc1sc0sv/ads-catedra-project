# Cobro y cierre

## What does this part of the system do?

Cierra el ciclo de la venta: cobra al cliente con el método elegido y, en una sola transacción, marca la venta como completada, descuenta del stock y deja el rastro contable en movimientos de inventario. Es el paso donde dinero, inventario y registro tienen que quedar perfectamente alineados o no quedar en absoluto.

Soporta efectivo, transferencia, débito y tarjeta. La tarjeta se procesa contra Stripe; el resto se registra como cobro local con el monto recibido. Al confirmar, el sistema entrega un comprobante imprimible o descargable.

## Who uses it?

El cajero, una vez que el carrito está armado y, si aplica, la receta está vinculada y validada.

## How does it work?

El cajero pasa al panel de cobro y elige método. Para efectivo, captura el monto recibido y el sistema le muestra el cambio. Para transferencia o débito, solo confirma. En estos métodos no se crea registro en la pasarela —se confía en el cajero y queda solo el método de pago anotado en la venta.

Para tarjeta, el sistema arma primero un registro de cobro Stripe en estado PENDIENTE y le asocia una idempotency key derivada de la venta. Esto se hace antes de hablar con Stripe. Si el cajero hace doble clic o el navegador reintenta, Stripe reconoce la misma key y no cobra dos veces. Al recibir respuesta, el registro se actualiza a EXITOSA con el charge id y el payment intent, o a FALLIDA con el mensaje de error correspondiente; en ese último caso la venta sigue en EN_PROCESO y el cajero puede reintentar o cambiar de método sin perder el carrito.

Cuando el pago confirma, el sistema arranca una transacción atómica que marca la venta como COMPLETADA, descuenta del stock por cada línea y crea un movimiento SALIDA_VENTA por línea con el usuario responsable, todo junto o nada. Si dentro de esa transacción algo falla —por ejemplo, otro cajero ya cerró su venta primero y agotó el stock— el sistema invoca automáticamente un refund a Stripe, deja el registro de cobro en estado REEMBOLSADA con el motivo "rollback automático" y devuelve la venta a EN_PROCESO con el error visible para el cajero. Así nunca queda dinero cobrado sin venta detrás.

Al éxito, se entrega el comprobante imprimible o descargable.

## Skills relevantes

- `/laravel-specialist` — para la transacción atómica venta→stock→movimientos
- `/laravel-patterns` — para el servicio que orquesta Stripe y el rollback si falla cualquier paso
- `/security-review` — para validar el manejo de claves de Stripe, la idempotencia del cobro y la protección ante doble-clic
