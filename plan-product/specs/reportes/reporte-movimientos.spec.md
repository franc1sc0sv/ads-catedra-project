# Reporte de Movimientos

## What does this part of the system do?
Expone el historial de cada movimiento de inventario, que es la traza fina de cómo cambió el stock a lo largo del tiempo. Sirve para investigar discrepancias: por ejemplo, cuando un conteo físico no cuadra con el sistema, el admin entra acá y revisa qué pasó con ese medicamento en los últimos días.

Cada fila muestra fecha, tipo de movimiento, medicamento, cantidad, stock antes y después, usuario responsable y origen (la venta o pedido vinculado, cuando aplica). Esa última columna es la que cierra el círculo: si una salida se generó por una venta concreta, el admin puede saltar a esa venta sin perder el contexto.

## Who uses it?
Solo el administrador, que es quien hace las investigaciones cuando algo no cuadra.

## How does it work?
La pantalla abre con los movimientos del día y de ahí el admin filtra por tipo (entrada por compra, salida por venta, ajuste manual, devolución o baja por vencimiento), medicamento, usuario responsable y rango de fechas. Los filtros se combinan, así la pregunta "¿qué ajustes manuales hizo el cajero X esta semana?" se contesta seleccionando tipo "ajuste manual", usuario "cajero X" y rango "esta semana". Para cada movimiento, los campos de stock antes y después dejan ver el efecto exacto sobre el inventario, lo cual es clave cuando se sospecha de un descuadre. Si el origen es una venta cancelada, el movimiento aparece igual porque la cancelación generó su propia entrada compensatoria — no se borra historia. La paginación es del lado del servidor para que rangos largos no carguen toda la tabla en memoria. La exportación a CSV respeta los filtros activos.

## Skills relevantes

- `/laravel-specialist` — para la query con filtros combinados y eager-loading de venta y pedido vinculados, evitando el N+1.
- `/tailwind-css-patterns` — para la tabla con badges diferenciados por tipo de movimiento que faciliten el escaneo visual.
