# Shape — Cobro y Cierre

## Núcleo del problema

Tres efectos colaterales (estado venta, stock, movimiento de inventario) deben ocurrir todos o ninguno. El riesgo: dos cajeros cerrando ventas que comparten el mismo producto al mismo tiempo, donde el segundo encuentra stock insuficiente recién al intentar descontar.

## Decisiones de shape

### Transacción atómica con locking pesimista
- `DB::transaction` envolviendo todo el cierre.
- `Venta::lockForUpdate()` al inicio para serializar reintentos sobre la misma venta.
- `Producto::lockForUpdate()` por línea al momento de validar y descontar stock — evita la race condition con otros cajeros.

### Idempotencia en la entrada del servicio
Primer chequeo dentro de la transacción: si `venta->estado !== EN_PROCESO`, retornar la venta tal cual sin tocar nada. Cubre:
- Doble click en el botón Cobrar.
- Reintento del cajero tras un error de red en el response (la venta ya quedó cerrada server-side).
- Bot/usuario malicioso reenviando el POST.

### Rollback semántico
En conflicto de stock, lanzar excepción de dominio (`StockInsuficienteException`). Laravel hace rollback automático. El controller la captura y devuelve `back()->withErrors()` con mensaje específico (producto + stock disponible). La venta sigue `EN_PROCESO` — el cajero puede ajustar el carrito y reintentar.

### Comprobante
Server-rendered Blade, no PDF. Vista separada (`comprobante.blade.php`) con CSS `@media print` y botón `onclick="window.print()"`. El navegador hace el resto. URL accesible solo si la venta está `COMPLETADA` y pertenece al salesperson (o se decide que cualquier salesperson puede reimprimir — TBD por owner).

### Cálculo de cambio
Solo cliente, en Alpine.js sobre el input `monto_recibido`. El server valida `gte:total` pero no recalcula cambio — no se persiste el cambio, solo el `monto_recibido` y el total ya guardado.

### MetodoPago como enum, no tabla
4 valores fijos, sin metadata adicional, sin pasarela. Enum backed PHP es suficiente. Si más adelante se agrega referencia bancaria por método, se promueve a tabla.

## Lo que NO se hace
- No se integra pasarela de pago.
- No se almacenan referencias bancarias / autorizaciones de tarjeta.
- No se genera PDF server-side.
- No se notifica por email al cliente.
- No se permite cobro parcial / split payment.
