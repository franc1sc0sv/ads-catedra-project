# Shape Notes — Reporte de Inventario

## Decisiones clave

- **KPI + tabla acoplados al mismo filter set.** Un solo formulario GET, un solo recálculo. No hay endpoints separados para KPIs y filas: el controller llama dos métodos del mismo service contra el mismo `array $filtros`.
- **Cómputo en servidor.** Postgres hace agregados y joins. Cliente solo renderiza. No se manda el dataset crudo al browser.
- **CSV streamed.** `StreamedResponse` con cursor sobre la query (sin paginar). Memoria constante para inventarios grandes.
- **Ventana de vencimiento default 30 días.** Selector 30/60/90. Afecta únicamente el KPI "próximos a vencer"; no recorta filas de la tabla — mantenerlo simple para el MVP.
- **Productos en cero visibles.** Decisión explícita: stock=0 es información accionable para el inventory_manager (qué reordenar). No se ocultan.
- **Filtros en query string.** URLs compartibles, export reusa el mismo string, sin estado de sesión.

## Apuestas y riesgos

- **Cálculo de "fecha de vencimiento más próxima"** asume que existe una tabla de lotes con `medicamento_id` + `fecha_vencimiento`. El service hace `MIN(fecha_vencimiento)` por medicamento. Si el schema modela vencimiento directo en `medicamentos`, simplificar a una columna.
- **Estado de stock derivado en servidor**, no persistido. Recalculado en cada query (`CASE WHEN stock=0 THEN 'cero' WHEN stock<=minimo THEN 'bajo_minimo' WHEN proximo_venc<NOW() THEN 'vencido' ELSE 'normal' END`). Costo de cómputo aceptable para volúmenes de farmacia.
- **Sin caché.** Reporte vive de la verdad actual. Si el volumen lo justifica más adelante, agregar caché por `(filtros_hash, día)`.

## Lo que NO hace

- No tendencias históricas (otro reporte si llega).
- No edita stock ni dispara compras.
- No expone roles fuera de administrator / inventory_manager.
- No tiene orden interactivo de columnas en MVP.

## Vistas duplicadas por rol

Por la convención de namespacing por rol, hay dos blades casi idénticas. El contenido real vive en un partial compartido (`components/reportes/inventario-content.blade.php`); cada vista de rol solo arma su layout y nav. Evita drift.
