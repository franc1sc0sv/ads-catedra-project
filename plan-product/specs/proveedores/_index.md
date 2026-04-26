# Proveedores y Pedidos

Esta sección es donde el encargado de inventario administra a las empresas que abastecen la farmacia y gestiona el ciclo completo de compra: solicitar mercancía, recibirla físicamente y dejar el stock actualizado en consecuencia. Toda la trazabilidad del flujo de compras vive aquí.

Distinguimos siempre lo solicitado de lo realmente recibido, porque en la realidad las dos cantidades pocas veces coinciden y necesitamos dejar registro fiel de ambas.

## What's inside this section

Cubre el catálogo de proveedores y los tres momentos del pedido: armarlo, recibirlo y consultarlo después.

- **catalogo-proveedores** — alta, edición y desactivación de las empresas proveedoras con su RFC único.
- **crear-pedido** — armar un pedido nuevo a un proveedor con sus líneas de medicamento, cantidad y precio estimado.
- **recibir-pedido** — capturar la cantidad realmente recibida, actualizar stock y dejar movimientos de entrada.
- **listado-pedidos** — tabla filtrable de pedidos con estados, detalle y opción de cancelar los aún solicitados.

## What data does this section work with?

Trabaja con proveedores, pedidos y sus líneas, referencia al catálogo de medicamentos, los usuarios que solicitan y reciben, y los movimientos de inventario que se generan al confirmar la recepción.

## What does this section depend on?

Depende de Autenticación y Roles para los permisos del encargado, y de Inventario porque cada línea de pedido necesita un medicamento del catálogo.

## Skills relevantes

- `/laravel-specialist` — para los modelos master-detail (Pedido + DetallePedido) y las relaciones con Proveedor.
- `/laravel-patterns` — para encapsular la recepción del pedido en un servicio que actualiza stock y crea movimientos en la misma transacción.
- `/tailwind-css-patterns` — para las tablas, el formulario de líneas y los badges de estado del pedido.
