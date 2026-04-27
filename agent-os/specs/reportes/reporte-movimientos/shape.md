# Shape — Reporte de Movimientos

## Problem

El administrador necesita investigar discrepancias de inventario. El stock actual no basta: hay que ver el rastro completo de cada cambio (quién, cuándo, por qué) y poder saltar al documento origen (venta o pedido) sin perder filtros.

## Approach

- Reporte tabular de solo lectura sobre la tabla `movimientos_inventario`.
- Filtros combinables aplicados como AND en la query base. Vacío = sin restricción para ese campo.
- Default: movimientos del día actual cuando no se envía rango de fechas.
- Paginación servidor (Laravel paginator) para soportar historiales grandes.
- Eager-load `medicamento`, `usuario`, `venta`, `pedido` para evitar N+1 al imprimir las filas y construir los enlaces de origen.
- Enlace de origen condicional según `tipo` y la relación poblada.
- CSV export usa la misma query con los mismos filtros, pero sin paginar — se transmite con `StreamedResponse` para no romper memoria si el rango es grande.

## Key constraints

- Combinable filters (tipo + medicamento + usuario + rango de fechas) — todos opcionales, todos compatibles.
- Eager-loading obligatorio para evitar N+1 en la tabla y en el cálculo de los enlaces de origen.
- Server-side pagination, no scroll infinito.
- CSV respeta exactamente los filtros activos del request.
- Movimientos asociados a ventas canceladas permanecen visibles. La cancelación generó un movimiento compensatorio (entrada), que también aparece en el listado. Ningún movimiento se borra ni se oculta.
- Solo `administrator` (middleware `role:administrator`).

## Open questions

- Volumen esperado de movimientos por día — define si el default "hoy" sigue siendo razonable o conviene un límite duro adicional.
- Tamaño de página por defecto (25? 50?) — ajustable según UX una vez con datos reales.
