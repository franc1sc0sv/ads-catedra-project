# Reporte de Movimientos

## Resumen

Listado paginado del historial de movimientos de inventario para investigar discrepancias. Cada fila muestra: fecha/hora, tipo de movimiento, medicamento, cantidad, stock antes, stock después, usuario responsable y origen (venta o pedido vinculado, con enlace para saltar al detalle). Solo accesible para el rol `administrator`.

## Alcance

- Vista por defecto: movimientos del día actual.
- Filtros combinables aplicados como AND:
  - Tipo: `entrada_compra`, `salida_venta`, `ajuste_manual`, `devolucion`, `baja_vencimiento`.
  - Medicamento (selector / búsqueda).
  - Usuario.
  - Rango de fechas (desde / hasta).
- Paginación del lado servidor para no romper en historiales largos.
- Eager-load de las relaciones `medicamento`, `usuario`, `venta`, `pedido` para evitar N+1 al renderizar cada fila y sus enlaces de origen.
- Enlaces de origen:
  - Movimientos `salida_venta` enlazan al detalle de la venta.
  - Movimientos `entrada_compra` enlazan al detalle del pedido.
  - Otros tipos no tienen enlace de origen.
- Movimientos generados por ventas canceladas siguen apareciendo. La cancelación produce un movimiento compensatorio (entrada) que también es visible; ambos quedan en el historial sin alterarse.
- Sin acciones de edición ni eliminación. Es un reporte de solo lectura.
- Exportación CSV que respeta exactamente los filtros activos.

## Fuera de alcance

- Edición o reversión manual de movimientos desde este reporte.
- Reporte agregado (totales, gráficas) — esto es histórico fila por fila.
- Acceso para roles distintos al administrador.
