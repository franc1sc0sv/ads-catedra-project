# Historial de Movimientos

## What does this part of the system do?
Para cualquier medicamento, muestra la cronología completa de cambios de su stock: desde la primera vez que se dio de alta hasta el último movimiento de hoy. Cada entrada por compra recibida, cada salida por venta, cada ajuste manual con su motivo, cada baja por vencimiento, cada devolución; todo en una línea de tiempo única.

Es la herramienta que responde la pregunta "¿por qué este producto tiene este stock hoy?" sin tener que adivinar. Y también es la herramienta de auditoría: si algo se ve raro —demasiados ajustes manuales en un mes, salidas que no encajan con las ventas— acá se ve.

A diferencia del catálogo (que muestra el ahora), el historial muestra el cómo se llegó al ahora.

## Who uses it?
El encargado de inventario para entender el estado de un producto, y el administrador para auditar movimientos sospechosos.

## How does it work?
Desde el detalle de un medicamento, se accede a su historial: una lista cronológica, normalmente de la más reciente a la más antigua, con cada movimiento del producto. Cada línea muestra la fecha y hora, el tipo (entrada por pedido, salida por venta, ajuste manual, baja por vencimiento, devolución), la cantidad que cambió, el stock antes del movimiento, el stock después, y el usuario responsable. Cuando el movimiento viene de una venta, se puede saltar al detalle de esa venta; cuando viene de un pedido, al detalle del pedido; cuando es un ajuste manual, se ve el motivo escrito por el encargado. Se puede acotar por rango de fechas y por tipo de movimiento, útil cuando el producto tiene años de historia y se busca algo específico. La pantalla es estrictamente de lectura: nada se puede editar ni borrar desde aquí. Si revisando el historial se descubre un error que hay que corregir, eso se hace en la pantalla de ajuste manual creando un movimiento compensatorio, que también quedará registrado en este historial.

## Skills relevantes

- `/laravel-specialist` — para la query con eager-loading de venta y pedido relacionados
- `/tailwind-css-patterns` — para la timeline o tabla cronológica
