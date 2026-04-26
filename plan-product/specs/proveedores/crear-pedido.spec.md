# Crear Pedido

## What does this part of the system do?
Permite al encargado armar un pedido nuevo a un proveedor, con todas las líneas de medicamento que necesite reabastecer. Cada línea lleva el medicamento, la cantidad solicitada y un precio unitario estimado, y el sistema calcula el total en vivo conforme se agregan o modifican líneas.

Al guardar, el pedido nace en estado SOLICITADO, queda asociado al usuario que lo creó y a la fecha de creación, listo para enviarse al proveedor o para recibirse cuando llegue la mercancía.

## Who uses it?
Solo el encargado de inventario.

## How does it work?
El encargado abre un pedido nuevo, elige un proveedor del catálogo (solo aparecen los activos) y empieza a agregar líneas. En cada línea selecciona un medicamento del catálogo y captura la cantidad solicitada y un precio de compra estimado — lo que cree que pagará por unidad — y el subtotal de la línea se calcula al instante con esos dos valores. El total estimado del pedido se obtiene sumando cantidad × precio estimado de cada línea y se guarda con el pedido como referencia económica del compromiso que se está armando. Un mismo medicamento solo puede ir una vez en el pedido: si lo intenta agregar de nuevo, el formulario lo bloquea para evitar duplicados que después compliquen la recepción. Además, el encargado puede agregar observaciones al pedido en un campo de texto libre opcional para dejar notas al proveedor, una fecha esperada de entrega o condiciones especiales que convenga conservar. Al guardar, el sistema persiste el pedido con su total estimado, sus observaciones y todas sus líneas en una sola transacción, registra al usuario creador y deja el pedido editable mientras siga en SOLICITADO. Una vez que cambia a ENVIADO o RECIBIDO, ya no se puede modificar; cualquier corrección se hace cancelando o ajustando inventario después.

## Skills relevantes

- `/laravel-specialist` — para el formulario master-detail (Pedido + líneas) con las validaciones por línea y la unicidad de medicamento dentro del pedido.
- `/laravel-patterns` — para el servicio que arma el pedido y sus líneas en una sola transacción y devuelve el pedido listo.
