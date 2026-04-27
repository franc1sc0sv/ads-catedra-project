# Cobro y cierre

## What does this part of the system do?

Cierra el ciclo de la venta: cobra al cliente con el método elegido y, en una sola transacción, marca la venta como completada, descuenta del stock y deja el rastro contable en movimientos de inventario. Es el paso donde dinero, inventario y registro tienen que quedar perfectamente alineados o no quedar en absoluto.

Soporta efectivo, transferencia, débito y tarjeta. Todos los métodos de pago se registran localmente; no hay integración con pasarela externa. Al confirmar, el sistema entrega un comprobante imprimible o descargable.

## Who uses it?

El cajero, una vez que el carrito está armado y, si aplica, todas las recetas están vinculadas y validadas.

## How does it work?

El cajero pasa al panel de cobro y elige método. Para efectivo, captura el monto recibido y el sistema le muestra el cambio. Para los demás métodos —transferencia, débito o tarjeta— el cajero confirma que el cobro fue realizado por el medio correspondiente. En todos los casos el sistema confía en la confirmación del cajero y registra únicamente el método de pago elegido en la venta.

Al confirmar el pago, el sistema arranca una transacción atómica que marca la venta como COMPLETADA, descuenta del stock por cada línea y crea un movimiento SALIDA_VENTA por línea con el usuario responsable, todo junto o nada. Si dentro de esa transacción algo falla —por ejemplo, otro cajero ya cerró su venta primero y agotó el stock— la transacción se revierte completa y la venta vuelve a EN_PROCESO con el error visible para el cajero, para que ajuste las cantidades o quite la línea afectada.

Al éxito, se entrega el comprobante imprimible o descargable.

## Skills relevantes

- `/laravel-specialist` — para la transacción atómica venta→stock→movimientos
- `/laravel-patterns` — para el servicio de cierre y el rollback si falla cualquier paso
- `/security-review` — para validar la protección ante doble-clic y la integridad del cierre
