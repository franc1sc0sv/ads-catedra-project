# Listado de Pedidos

## What does this part of the system do?
Es la vista central donde se consultan todos los pedidos de la farmacia, con filtros por estado, proveedor y rango de fechas. Cada fila resume el pedido y desde ahí se entra al detalle completo para ver las líneas con cantidades solicitadas y recibidas lado a lado.

También es el punto de entrada para cancelar un pedido que aún no ha sido recibido, dejando registro del motivo de cancelación.

## Who uses it?
El encargado de inventario lo opera; el administrador lo consulta en modo lectura.

## How does it work?
La tabla muestra todos los pedidos con su número, proveedor, fecha, total estimado, estado (SOLICITADO, ENVIADO, RECIBIDO, CANCELADO) y el usuario que lo solicitó, y permite filtrar combinando los tres ejes: estado, proveedor y rango de fechas. Al abrir un pedido se ve el detalle con las líneas; si ya fue recibido, cada línea muestra la cantidad solicitada junto a la cantidad recibida para que la diferencia sea evidente. Desde la fila o el detalle, si el pedido está en SOLICITADO, el encargado puede cancelarlo escribiendo un motivo: el pedido pasa a CANCELADO y, como nunca movió stock, no afecta inventario. Cancelar un pedido ya RECIBIDO no es posible, porque ya tocó el stock; el camino correcto en ese caso es hacer un ajuste manual de inventario desde la sección de Inventario.

## Skills relevantes

- `/laravel-specialist` — para las queries con filtros combinables y el detalle eager-loaded de pedido, líneas y proveedor.
- `/tailwind-css-patterns` — para la tabla con badges de estado por color y la vista de detalle con cantidades comparadas.
