# Recibir Pedido

## Overview

Cierra el ciclo de compra a proveedor. El encargado de inventario captura, por cada línea del pedido, cuánto se recibió fisicamente y el precio real facturado. Al confirmar, el sistema actualiza stock, registra movimientos de inventario y marca el pedido como `RECIBIDO` — todo dentro de una transacción única, atómica.

Restringido a `inventory_manager`. Aplica solo a pedidos en estado `SOLICITADO` o `ENVIADO`.

## Flujo

1. Usuario abre `Pedidos > Detalle > Recibir` para un pedido en `SOLICITADO` o `ENVIADO`.
2. La vista lista cada línea (`DetallePedido`) con su medicamento, cantidad solicitada y precio estimado.
3. Por cada línea el usuario captura:
   - `nCantidadRecibida` — entero `>= 0`. Puede ser **menor**, **igual** o **mayor** que `nCantidadSolicitada`.
   - `nPrecioReal` — opcional; si se omite, se asume el precio estimado de la línea.
4. Al confirmar (`POST`), el `PedidoService::recibir()` envuelve todo en `DB::transaction(function () { ... })`:
   - Actualiza `Pedido`: `cveEstado = RECIBIDO`, `cveUsuarioReceptor = auth()->id()`, `fRecepcion = now()`.
   - Para cada línea:
     - Persiste `nCantidadRecibida` y `nPrecioReal` en el `DetallePedido`.
     - Suma `nCantidadRecibida` al `nStock` del `Medicamento`.
     - Crea un `MovimientoInventario` tipo `ENTRADA_COMPRA` con FK `cvePedido` para trazabilidad.
5. Si cualquier paso falla, la transacción hace rollback y nada se persiste — ni stock, ni movimientos, ni cambio de estado.

## Reglas

- **Entrega parcial es válida.** `nCantidadRecibida < nCantidadSolicitada` no es un error; el pedido pasa igualmente a `RECIBIDO` y solo se suma al stock lo realmente recibido.
- **Recibir-con-cero.** Para pedidos `ENVIADO` que nunca llegaron o se perdieron, el encargado puede capturar `nCantidadRecibida = 0` en todas las líneas y cerrar el pedido como `RECIBIDO` sin afectar stock — con el rastro auditable de quién y cuándo.
- **Sobre-entrega permitida.** `nCantidadRecibida > nCantidadSolicitada` se acepta; el stock refleja lo que realmente entró.
- **Precio real por línea.** `nPrecioReal` se almacena en `DetallePedido`, no en el `Pedido`. No se recalcula un total agregado en el pedido — la fuente de verdad por línea queda íntegra para auditoría y reportes de costos.
- **Movimiento atado al pedido.** Cada `MovimientoInventario` creado lleva `cvePedido` para rastrear el origen exacto de la entrada de stock desde reportes de inventario.

## Restricciones

- Solo `inventory_manager` (middleware `role:inventory_manager`).
- Solo `web` (sesión de Laravel). No hay endpoints API.
- Estados de origen válidos: `SOLICITADO`, `ENVIADO`. Cualquier otro estado retorna `redirect()->back()` con error.
- `declare(strict_types=1)`, constructor readonly, `View|RedirectResponse` en el controller.
