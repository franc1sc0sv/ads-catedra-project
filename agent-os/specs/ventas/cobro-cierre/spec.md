# Cobro y Cierre

## Resumen

Cierra el ciclo de venta: cobra al cliente con el método elegido y, en una sola transacción atómica, marca la venta como COMPLETADA, descuenta stock por línea y crea los movimientos de inventario correspondientes. No hay integración con pasarelas externas; el sistema solo registra el método de pago.

Solo el cajero (`salesperson`) puede ejecutarlo, y únicamente cuando el carrito está armado y, si aplica, las recetas ya fueron validadas.

## Métodos de pago

Enum `MetodoPago` con cuatro valores:

- `efectivo`
- `transferencia`
- `debito`
- `tarjeta`

Para `efectivo`, el formulario captura `monto_recibido` y muestra el cambio calculado en cliente (`monto_recibido - total`). Para los otros métodos, el cajero confirma la operación externa y el sistema solo registra el método; no se almacena referencia bancaria.

## Transacción atómica de cierre

Al confirmar el cobro, dentro de un único `DB::transaction`:

1. Se relee la venta con bloqueo (`lockForUpdate`) y se valida que esté en `EN_PROCESO`. Si está en otro estado, se aborta sin tocar nada (idempotencia).
2. Por cada línea de la venta, se relee el producto con `lockForUpdate` y se verifica stock disponible.
3. Se descuenta el stock del producto.
4. Se crea un movimiento `SALIDA_VENTA` por línea, con `usuario_responsable_id = auth()->id()` y referencia a la venta.
5. Se actualiza la venta: `estado = COMPLETADA`, `metodo_pago`, `monto_recibido` (si aplica), `cerrada_en = now()`.

Si cualquier paso falla (ej. stock agotado por otro cajero en paralelo), se hace rollback completo: la venta queda en `EN_PROCESO` y se devuelve un error visible (flash en la vista de carrito) indicando la línea conflictiva.

## Idempotencia

La protección contra doble click se aplica en la entrada del servicio: si la venta ya está `COMPLETADA`, el método retorna sin efectos secundarios. Esto cubre tanto el doble submit del formulario como reintentos accidentales.

## Comprobante

Al éxito, se redirige a una vista server-rendered (`comprobante.blade.php`) imprimible/descargable, con: número de venta, fecha, líneas, totales, método de pago, monto recibido y cambio (solo efectivo), y datos del cajero. Sin PDF generator: se imprime con `window.print()` desde el navegador.

## Alcance

- Ruta: `POST /salesperson/ventas/{venta}/cobrar` y `GET /salesperson/ventas/{venta}/comprobante`.
- Middleware: `auth`, `role:salesperson`.
- Sin bitácora en venta exitosa; solo registrar fallos manejados (stock conflict) si la política de bitácora lo cubre.
