# Recibir Pedido

## What does this part of the system do?
Cierra el ciclo de compra: cuando la mercancía llega físicamente a la bodega, el encargado captura por cada línea cuánto recibió de verdad, y el sistema actualiza el stock, deja registro de quién recibió y cuándo, y genera los movimientos de inventario correspondientes.

Es el punto donde lo solicitado se convierte en stock real, y donde la diferencia entre lo pedido y lo recibido queda asentada para siempre. Toda la operación corre en una sola transacción para que nunca queden inconsistencias entre el pedido, el stock y los movimientos.

## Who uses it?
Solo el encargado de inventario.

## How does it work?
El encargado abre un pedido que esté en SOLICITADO o ENVIADO y, línea por línea, captura la cantidad realmente recibida. Esa cantidad puede ser menor que la solicitada (entrega parcial), igual (entrega completa) o incluso mayor (sobreentrega). En la misma pantalla, por cada línea, el encargado puede ajustar el precio de compra real si difiere del estimado que se había puesto al armar el pedido — por ejemplo, porque el proveedor cambió la tarifa o porque el pedido original llevaba una estimación gruesa. Ese precio real se guarda contra la línea recibida y queda como referencia histórica del costo de la mercancía que efectivamente entró a la farmacia, sin tocar el total estimado original que sirve para comparar después. Al confirmar la recepción, el sistema marca el pedido como RECIBIDO, registra al usuario que recibe y la fecha, suma al stock de cada medicamento la cantidad recibida y crea un movimiento de inventario tipo ENTRADA_COMPRA por cada línea que hereda esa cantidad y queda atado al pedido para trazabilidad. Todo eso ocurre dentro de una misma transacción: si algo falla a medio camino, nada se persiste, para evitar que el stock quede actualizado pero el pedido sin marcar, o viceversa. Si la entrega fue parcial, el pedido se recibe igual en una sola pasada con cantidades menores, y queda registro permanente de que faltó mercancía respecto a lo solicitado.

## Skills relevantes

- `/laravel-specialist` — para la transacción que actualiza el pedido, suma stock al medicamento y crea los movimientos en cascada.
- `/laravel-patterns` — para encapsular toda la lógica de recepción en un servicio testeable y aislar los efectos secundarios.
