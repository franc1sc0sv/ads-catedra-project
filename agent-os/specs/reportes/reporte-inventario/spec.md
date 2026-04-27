# Reporte de Inventario

## Propósito

Foto del estado actual del inventario para administrador y encargado de inventario. Soporta decisiones de compra (qué reordenar) y de baja (qué retirar). KPIs en la parte superior, tabla detallada paginada abajo. Toda la pantalla obedece al mismo conjunto de filtros y los recalcula en servidor.

## Audiencia y Acceso

Roles: `administrator` y `inventory_manager`. La ruta web aplica `auth` + `role:administrator,inventory_manager`. Vendedor y farmacéutico no entran a este reporte.

## KPIs (encabezado)

Cinco indicadores calculados en servidor sobre el universo filtrado:

1. **Cantidad total de productos activos** — `COUNT` de medicamentos cuyo estado es activo dentro del filtro.
2. **Valor estimado del inventario** — `SUM(precio_unitario × stock_actual)` sobre el filtro.
3. **Productos bajo mínimo** — `COUNT` donde `stock_actual <= stock_minimo`. Incluye productos en cero.
4. **Próximos a vencer** — `COUNT` de medicamentos con la fecha de vencimiento más próxima cayendo dentro de la ventana configurable (30 / 60 / 90 días desde hoy). El valor por defecto es 30.
5. **Vencidos pendientes de baja** — `COUNT` de medicamentos con vencimiento anterior a hoy y que aún no han sido dados de baja.

Cada KPI respeta los filtros activos. Si el filtro deja la categoría X, los KPIs se recalculan solo sobre X.

## Tabla detallada

Una fila por medicamento dentro del filtro. Productos en cero también aparecen (no se ocultan). Columnas:

- Nombre del medicamento
- Categoría
- Stock actual
- Stock mínimo
- Fecha de vencimiento más próxima (lote más cercano a vencer)
- Proveedor (join con tabla de proveedores)
- Valor unitario (precio)
- Estado de stock (normal / bajo mínimo / cero / vencido) — derivado en servidor

La tabla es paginada del lado del servidor. Orden por defecto: nombre ascendente. Sin orden interactivo en MVP.

## Filtros

Los filtros viven en el query string para que sean compartibles y respetados por el export:

- `categoria_id` — categoría del medicamento.
- `proveedor_id` — proveedor.
- `estado_stock` — `normal | bajo_minimo | cero | vencido`.
- `ventana_vencimiento` — 30 | 60 | 90 (días). Default 30. Solo afecta el KPI de próximos a vencer; no filtra filas.

Cambiar cualquier filtro recarga la página vía GET, recalcula KPIs y refresca la tabla. Sin estado en sesión.

## Exportación CSV

Botón "Exportar CSV" reusa exactamente el mismo query string. El service genera un `StreamedResponse` con las mismas filas que se mostrarían (sin paginación — todas las filas del filtro). Mismas columnas que la tabla. Encabezado UTF-8 BOM para Excel.

## Flujo

1. Usuario entra a `/reportes/inventario` (o variante por rol).
2. Controller valida filtros, llama a `ReporteInventarioServiceInterface::computeKPIs($filtros)` y `getRows($filtros, $paginaActual)`.
3. Vista renderiza KPIs y tabla. Form de filtros hace GET a la misma ruta.
4. Click en "Exportar CSV" → `/reportes/inventario/export?...` → `exportCsv($filtros)` devuelve `StreamedResponse`.

## Fuera de alcance

- Gráficos de tendencia histórica (esto es solo foto actual).
- Edición de stock o creación de orden de compra desde el reporte.
- Roles ajenos a administrator / inventory_manager.
