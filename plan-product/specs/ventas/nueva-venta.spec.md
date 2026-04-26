# Nueva venta

## What does this part of the system do?

Permite al cajero abrir una venta nueva y armar el carrito producto por producto, con verificación de stock en cada agregado. La venta nace como anónima ("venta rápida") y opcionalmente se le asocia un cliente, ya sea buscándolo o creándolo en el momento si no existe en la base.

Mientras se construye, la venta queda persistida en estado EN_PROCESO. Esto significa que si el cajero es interrumpido —llamada, corte de luz, otro cliente— puede volver a la misma venta más tarde sin perder lo que ya había agregado. Subtotal, impuesto y total se recalculan en vivo conforme cambia el carrito.

## Who uses it?

El cajero, durante la atención directa al cliente en mostrador.

## How does it work?

El cajero abre el POS y arranca con una venta vacía marcada como anónima. Si el cliente quiere factura o seguimiento, busca por nombre o DUI; si no existe, lo crea ahí mismo con los datos mínimos y queda vinculado. Después busca productos por nombre o código de barras y los va agregando con la cantidad. El total se actualiza al momento.

La venta se persiste como EN_PROCESO desde que se agrega la primera línea, y cada cambio del carrito —agregar una línea, modificar la cantidad, asociar un cliente— se guarda inmediatamente. Así, si el cajero pierde la sesión, cierra el navegador o cambia de turno, puede retomar la venta exactamente donde la dejó, sin perder nada.

El precio unitario se congela en la línea cuando el producto se agrega al carrito. Si más tarde el administrador ajusta el precio del medicamento en el catálogo, la venta en curso conserva el precio que el cliente vio al momento de agregarlo, evitando sorpresas al cobrar.

El stock se verifica al agregar cada línea: si pide más de lo que hay, el sistema bloquea la cantidad y le muestra cuánto queda, dejándolo ajustar o quitar el producto. Pero el stock no se reserva mientras el carrito está abierto; el descuento real ocurre al cerrar la venta. Si dos cajeros venden la última unidad simultáneamente, el primero que confirma el cobro se la lleva, y el segundo recibe un error al cerrar y debe ajustar la cantidad o quitar la línea para seguir.

## Skills relevantes

- `/laravel-specialist` — para la persistencia del carrito en estado EN_PROCESO y la verificación de stock en cada agregado
- `/frontend-design` — para que el flujo de búsqueda y agregado se sienta inmediato bajo presión real
- `/tailwind-css-patterns` — para el layout del POS con carrito a un lado y búsqueda al otro
