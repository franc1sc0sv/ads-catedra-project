# Shape — Nueva Venta

## Decisiones de diseño

### Persist `EN_PROCESO` desde la primera línea

**Por qué:** el cajero es interrumpido todo el tiempo (cliente que cambia de opinión, llamada, atender otra caja). Mantener la venta en memoria de sesión sería frágil. Persistirla `EN_PROCESO` desde el primer producto agregado garantiza que al reabrir el POS encuentre su carrito intacto.

**Cómo:** `addLine` detecta si la venta no existe y la crea en ese mismo flujo, en una sola transacción con la inserción de la línea. La operación `open` solo busca; no crea ventas vacías.

### Sin reserva de stock

**Por qué:** reservar stock al agregar al carrito introduce complejidad (timeouts, liberación al cancelar, conflicto con cajeros simultáneos vendiendo el mismo medicamento). Para el MVP se prefiere modelo optimista: verificar al agregar (UX informativa) y resolver al cerrar.

**Consecuencia:** dos cajeros pueden tener el mismo medicamento en sus carritos. El primero en cerrar gana; el segundo recibe error de stock al intentar cerrar y debe ajustar líneas. La verificación al agregar es solo informativa, no garantiza disponibilidad.

### Precio congelado en la línea

**Por qué:** si Inventory cambia el precio del medicamento mientras una venta está `EN_PROCESO`, la venta no debe verse afectada — el cajero ya cotizó al cliente con el precio original.

**Cómo:** `nPrecioUnitario` se copia del catálogo al insertar la línea y se persiste en `DetalleVenta`. El recálculo de totales usa siempre el valor de la línea, nunca relee del catálogo.

### Increment-on-duplicate forzado por unique constraint

**Por qué:** simplifica el modelo del carrito (una línea por medicamento) y evita carritos sucios con duplicados.

**Cómo:** índice único `(cveVenta, cveMedicamento)` a nivel DB. El servicio en `addLine` consulta primero si la línea existe; si sí, hace `update nCantidad += cantidad`; si no, `insert`. La unique constraint es la red de seguridad.

### Anonymous-by-default, cliente opcional

**Por qué:** en farmacia, la mayoría de ventas son rápidas y no requieren cliente registrado. Forzar a seleccionar cliente fricciona el flujo principal.

**Cómo:** `cveCliente` nullable. La operación `attachClient` es independiente y se invoca desde un botón explícito en el POS. La venta nunca está bloqueada por falta de cliente.

## Fuera de alcance

- Cierre de venta y selección de método de pago (otra feature).
- Descuento real de stock (al cierre).
- Comprobante / impresión.
- Cancelación explícita de venta `EN_PROCESO`.
