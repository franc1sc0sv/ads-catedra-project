# Historial de Compras del Cliente

## What does this part of the system do?
Muestra todas las ventas que un cliente ha hecho en la farmacia, ordenadas de la más reciente a la más antigua. Es la vista que responde a la pregunta "¿qué ha comprado esta persona y cuándo?".

Sirve tanto para responder consultas que el propio cliente trae al mostrador ("¿cuándo me llevé tal medicamento?") como para que el cajero o el administrador entienda patrones de consumo antes de hacer una recomendación.

Vive dentro del detalle del cliente, así que se llega de forma natural desde el catálogo o desde la pantalla de venta cuando se quiere revisar a quién se está atendiendo.

## Who uses it?
Cajeros y administradores.

## How does it work?
Al abrir la ficha de un cliente, una de las pestañas o secciones es el historial. Ahí se ve una tabla cronológica con una fila por venta, cada una mostrando fecha, total, método de pago y estado (completada o cancelada). Al hacer clic en una fila se navega al detalle de esa venta con todas sus líneas — qué productos se llevó, cantidades y precios. Si el cliente no tiene ventas todavía, se muestra un mensaje vacío indicando que aún no ha comprado nada. Las ventas canceladas aparecen en el listado pero con un indicador visual claro para que no se confundan con compras reales, porque para entender el comportamiento del cliente importa saber qué intentó comprar y devolvió. La carga es eficiente: la tabla pagina las ventas y carga el detalle solo cuando se entra a una fila, para que un cliente con muchas compras no haga lenta la pantalla.

## Skills relevantes

- `/laravel-specialist` — para la relación inversa de venta a cliente con eager-loading del detalle al entrar a una venta.
- `/tailwind-css-patterns` — para la tabla cronológica del historial y el indicador visual de ventas canceladas.
