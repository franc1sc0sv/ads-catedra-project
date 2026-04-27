# Shaping Notes: Cancelar Venta

## Two routes, two roles, two scenarios

Cancelación no es una operación uniforme. El estado de la venta dicta el flujo, el rol y la ruta.

| Estado venta | Ruta | Rol | Transacción | Motivo |
|---|---|---|---|---|
| `EN_PROCESO` | `/salesperson/ventas/{venta}/cancel-en-proceso` | `salesperson` | No | No |
| `COMPLETADA` | `/admin/ventas/{venta}/cancel` | `administrator` | Sí | Sí (obligatorio) |

Razón de separar rutas: el middleware `role:` autoriza por ruta. Mezclar las dos en una sola ruta requeriría lógica de rol dentro del controller, lo cual viola la convención del proyecto (`EnsureRole` lee `auth()->user()->role->value` y bloquea en middleware).

## Transacción solo en COMPLETADA

`EN_PROCESO` no movió stock ni cobró, así que cancelar es un `update` de un campo. No hay nada que revertir.

`COMPLETADA` requiere atomicidad estricta:
- Si falla la generación de un `MovimientoInventario`, no se debe marcar la venta como `CANCELADA`.
- Si falla el update de stock en un lote, todos los movimientos previos deben revertirse.
- `DB::transaction()` sobre el closure que hace todo el trabajo.

## DEVOLUCION lleva cveVenta para distinguir origen

`MovimientoInventario` puede generarse por dos vías:
1. Cancelación de venta — este flujo. `cveVenta` poblado, apunta a la venta cancelada.
2. Ajuste manual desde `ajuste-stock` — `cveVenta = NULL`.

Esta distinción es crítica para auditoría: permite filtrar movimientos por origen sin necesidad de un campo extra. Reportes de "devoluciones por cancelación" hacen `WHERE tipo = 'DEVOLUCION' AND cve_venta IS NOT NULL`.

## Total only

No se cancela por línea. El UX y la lógica de negocio se simplifican drásticamente al excluir cancelación parcial. Si en el futuro se requiere "anular un ítem de la venta", será una feature distinta (probablemente una nota de crédito), no una extensión de este flujo.

## Recetas y comprobantes intactos

- `VentaReceta` no se borra: histórico inmutable.
- Recetas en estado `VALIDADA` quedan re-vinculables (la venta cancelada no las "consume").
- El comprobante asociado se marca anulado pero permanece accesible para consulta.

## Bitácora obligatoria en ambos flujos

Aunque `EN_PROCESO` sea trivial, también pasa por `BitacoraServiceInterface`. La trazabilidad de cancelaciones — incluso de ventas que nunca afectaron stock — es valiosa para detectar patrones de fraude (ej. cajero que abre y cancela ventas repetidamente).
