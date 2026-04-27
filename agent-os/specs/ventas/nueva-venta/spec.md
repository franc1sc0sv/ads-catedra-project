# Nueva Venta

## Resumen

Cajero (rol `salesperson`) abre una venta y arma un carrito producto por producto desde la pantalla POS. La venta nace anónima ("venta rápida"); opcionalmente se asocia un cliente buscándolo o creándolo. Mientras se construye, la venta queda persistida en estado `EN_PROCESO` desde la primera línea agregada, de modo que si el cajero es interrumpido pueda retomarla sin pérdida. Subtotal, impuesto y total se recalculan en vivo en cada cambio del carrito. Solo el rol `salesperson` puede operar el POS.

## Modelo de datos

### `RegistroVentas`

Cabecera de venta. Campos:

- `cveVenta` — PK.
- `eEstado` — enum `EstadoVenta` con valores `EN_PROCESO`, `COMPLETADA`, `CANCELADA`. Nace en `EN_PROCESO` al crear la primera línea.
- `cveCliente` — FK a `Cliente`, nullable. Una venta sin cliente asociado es una "venta rápida" anónima.
- `nSubtotal` — decimal, suma de líneas antes de impuestos. Recalculado en cada cambio.
- `nImpuesto` — decimal, monto de impuesto calculado sobre el subtotal.
- `nTotal` — decimal, `nSubtotal + nImpuesto`.
- `eMetodoPago` — enum, nullable. Permanece nulo mientras la venta esté `EN_PROCESO`; se fija al cerrar (fuera del alcance de esta feature).
- `cveUsuarioCajero` — FK al `User` cajero que abrió la venta.
- `fCreado` — timestamp de apertura.

### `DetalleVenta`

Línea de carrito. Campos:

- `cveDetalle` — PK.
- `cveVenta` — FK a `RegistroVentas`.
- `cveMedicamento` — FK a `Medicamento`.
- `nCantidad` — entero positivo.
- `nPrecioUnitario` — decimal, **congelado al momento de agregar la línea**. Cambios posteriores en el catálogo no afectan ventas en curso.
- Restricción única `(cveVenta, cveMedicamento)`.

## Reglas de negocio

### Persist-on-first-line

La venta no existe hasta que el cajero agrega el primer producto. Al agregar la primera línea, el servicio crea `RegistroVentas` con `eEstado=EN_PROCESO` y luego inserta la línea. Si el cajero cierra la pestaña o se interrumpe, la próxima vez que abra el POS retoma la venta `EN_PROCESO` que tenga abierta como cajero.

### Stock no reservado

Al agregar o incrementar una línea, el servicio verifica que haya stock disponible (consulta de lectura), pero **no reserva** stock. La reserva/descuento ocurre solo al cerrar la venta (fuera del alcance de esta feature). Modelo de concurrencia: primer cajero en cerrar gana, segundo recibe error de stock insuficiente y debe ajustar líneas.

### Precio congelado en la línea

`nPrecioUnitario` se copia desde el catálogo al insertar la línea y se persiste en `DetalleVenta`. El servicio nunca relee el precio del catálogo para totales: usa siempre el precio almacenado en la línea.

### Increment-on-duplicate

La restricción única `(cveVenta, cveMedicamento)` impide insertar dos líneas para el mismo producto. Cuando el cajero agrega un producto que ya está en el carrito, el servicio detecta la línea existente e **incrementa** su `nCantidad` en lugar de crear una nueva. La verificación de stock se hace contra la cantidad final resultante.

### Recalculo de totales

Después de cada operación que modifique líneas (`addLine`, `updateLine`, `removeLine`), el servicio recalcula `nSubtotal`, `nImpuesto` y `nTotal` y los persiste en la cabecera.

### Cliente opcional

La venta nace sin cliente. El cajero puede asociar un cliente vía `attachClient` en cualquier momento mientras la venta esté `EN_PROCESO`. Se acepta seleccionar un cliente existente o crear uno nuevo.

## Operaciones del servicio

- `open(cajero)` — devuelve la venta `EN_PROCESO` del cajero si existe; si no, no crea nada hasta la primera línea.
- `addLine(venta, medicamento, cantidad)` — crea venta si no existe, inserta o incrementa línea, congela precio, verifica stock, recalcula totales.
- `updateLine(detalle, cantidad)` — cambia cantidad, verifica stock, recalcula.
- `removeLine(detalle)` — borra línea, recalcula.
- `attachClient(venta, cliente)` — asocia cliente a la venta `EN_PROCESO`.
- `recalculateTotal(venta)` — interno, invocado por las anteriores.

## Alcance

Incluye: apertura, gestión del carrito, asociación de cliente, totales en vivo, persistencia `EN_PROCESO`.
No incluye: cierre de venta, método de pago, descuento de stock, impresión de comprobante.
