# Cancelar Venta

## Resumen

Cancelación de ventas con dos flujos diferenciados según el estado de la venta. La cancelación es siempre total — nunca por línea. El histórico nunca se borra: la venta queda con estado `CANCELADA` y el comprobante permanece accesible aunque marcado como anulado.

## Escenario 1: Venta `EN_PROCESO`

Operación trivial. La venta no descontó stock ni registró cobro, por lo que no requiere reversión.

- Rol autorizado: `salesperson` (cajero).
- Acción: cambia estado de `EN_PROCESO` a `CANCELADA`.
- Sin transacción de base de datos compleja, sin movimientos de inventario.
- Sin motivo obligatorio.
- Audit log via `BitacoraServiceInterface` con acción `VENTA_CANCELADA`.

## Escenario 2: Venta `COMPLETADA`

Operación sensible. La venta ya descontó stock y registró cobro. Requiere devolución a inventario y trazabilidad estricta.

- Rol autorizado: `administrator`.
- Motivo obligatorio (texto, validado en `CancelCompletadaRequest`).
- Toda la lógica corre dentro de `DB::transaction()`:
  1. Por cada línea de la venta, generar un `MovimientoInventario` tipo `DEVOLUCION` con `cveVenta` poblado (apunta a la venta original). Esto distingue las devoluciones por cancelación de los movimientos manuales generados desde `ajuste-stock`, que llevan `cveVenta = NULL`.
  2. Sumar las unidades de cada línea al stock del lote correspondiente.
  3. Marcar la venta como `CANCELADA`.
  4. Registrar entrada en bitácora vía `BitacoraServiceInterface` con acción `VENTA_CANCELADA` y referencia al motivo.
- Cancelación total únicamente — no se permite cancelar líneas individuales.

## Recetas vinculadas

Si la venta tenía recetas asociadas, las filas en `VentaReceta` se conservan como histórico. Las recetas que estaban en estado `VALIDADA` quedan disponibles para re-vincular a una nueva venta.

## Comprobante

El comprobante asociado a la venta cancelada permanece accesible para consulta y auditoría, pero queda marcado como anulado. No se borran filas.

## Restricciones

- Web-only. Sesión Laravel. Sin endpoints API.
- `declare(strict_types=1)` en todos los archivos PHP.
- Constructor readonly con promoción para inyección de dependencias.
- Controllers retornan `View|RedirectResponse`; toda la lógica de negocio en el servicio.
- Service-interface pattern: `VentaServiceInterface` + `VentaService` con binding en `AppServiceProvider`.
