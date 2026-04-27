# Ventas (POS)

El punto de venta es el corazón operativo de FarmaSys. Aquí el cajero atiende al cliente real: arma el carrito verificando stock en vivo, asocia (o no) un cliente, valida la receta cuando hay medicamentos controlados, cobra con efectivo, transferencia, débito o tarjeta y cierra la transacción dejando todo consistente.

Es el flujo más crítico del sistema porque toca dinero, inventario y cumplimiento regulatorio en un solo paso. Si algo aquí falla a medias, el negocio queda con stock desfasado, cobros sin venta o ventas sin cobro.

## What's inside this section

Cuatro sub-secciones cubren el ciclo completo de una venta, desde que se abre hasta que eventualmente se cancela.

- **nueva-venta** — el cajero arma el carrito, vincula un cliente opcional y verifica stock en vivo
- **cobro-cierre** — selección de método de pago, cobro local y cierre transaccional con descuento de stock
- **adjuntar-receta** — captura y vinculación de receta para medicamentos controlados, con validación del farmacéutico
- **cancelar-venta** — cancelación rápida en proceso o reversa completa de una venta ya cerrada por parte del administrador

## What data does this section work with?

Trabaja sobre la venta y sus líneas, lee stock y catálogo de medicamentos, opcionalmente vincula un cliente y, cuando hay controlados, vincula las recetas correspondientes a través de `VentaReceta`. Al cerrar genera movimientos de inventario tipo SALIDA_VENTA (o DEVOLUCION en cancelaciones). El método de pago queda anotado en la venta; todos los cobros se registran localmente.

## What does this section depend on?

Depende de Autenticación y Roles, Inventario, Clientes y Recetas.

## Skills relevantes

- `/laravel-specialist` — para los modelos transaccionales de venta y el guard del rol Cajero
- `/laravel-patterns` — para el servicio de cierre que actualiza stock, crea movimientos y registra el pago en una sola transacción
- `/frontend-design` — para que la pantalla del POS se sienta rápida y clara bajo presión real
- `/tailwind-css-patterns` — para el carrito, la tabla de líneas, el panel de cobro y los estados visuales
