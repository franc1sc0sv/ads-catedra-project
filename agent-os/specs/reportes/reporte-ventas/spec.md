# Reporte de Ventas

Vista resumida y detallada de ventas para el rol administrador, en un rango de fechas con filtros opcionales. Toda la pantalla (KPIs + tablas) responde al mismo conjunto de filtros y se recalcula al cambiarlos.

## Alcance

Solo administrador. La ruta vive bajo `role:administrator`. Vendedores, inventario y farmacéutico no tienen acceso a esta vista.

## Filtros

- Rango de fechas (`desde`, `hasta`). Default: primer día del mes en curso hasta hoy.
- Método de pago (opcional, multi-valor o uno solo según UI).
- Vendedor / cajero (opcional, FK al usuario salesperson).
- Estado de venta: `completada`, `cancelada`, `ambas`. Default: `completada`.

Todos los filtros se envían por query string y la página re-renderiza con los datos recalculados.

## KPIs

Cinco tarjetas calculadas vía SQL aggregates sobre el conjunto filtrado:

1. **Cantidad de ventas** — count de ventas en estado `completada` dentro del filtro.
2. **Ingreso total** — suma de totales de ventas `completada`. Las canceladas no inflan ingreso.
3. **Ticket promedio** — ingreso total / cantidad de ventas completadas (0 si no hay ventas).
4. **Cancelaciones** — count de ventas en estado `cancelada` dentro del rango.
5. **Monto cancelado** — suma de totales de ventas canceladas (métrica separada, no resta del ingreso).

## Tablas

### Ventas

Listado paginado de ventas que matchean los filtros. Columnas mínimas: fecha, número de venta, vendedor, método de pago, estado, total. Ordenado por fecha desc.

### Top productos

Agrupado por medicamento (no por SKU/lote individual), suma de unidades vendidas en el rango filtrado, ordenado por unidades desc. Limit configurable (ej. 10). Solo considera ventas `completada`.

## Exportación CSV

Botón de exportar genera CSV con las ventas del listado respetando exactamente los filtros activos (rango, método de pago, vendedor, estado). Stream para no cargar todo en memoria.

## Reglas

- Cancelaciones siempre se muestran como métrica separada; nunca se restan del ingreso.
- Si no hay ventas completadas en el rango, ticket promedio = 0.
- El filtro de estado `ambas` muestra ambas en la tabla pero los KPIs siguen separando completadas (1, 2, 3) de canceladas (4, 5).
