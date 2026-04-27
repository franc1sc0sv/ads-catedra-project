# Shape — Reporte de Ventas

## Core decisions

- **Single filter set drives everything.** KPIs, ventas table, and top productos all read from the same `$filters` array. No widget queries its own filters. Avoids drift between cards and tables.
- **Cancelled sales are a separate metric, never subtracted.** KPIs 1–3 (cantidad, ingreso, ticket promedio) compute over `estado = completada`. KPIs 4–5 (cancelaciones, monto cancelado) compute over `estado = cancelada`. The `ambas` filter only affects the ventas table; KPIs always partition by estado internally.
- **Top productos grouped by medicamento, not by SKU/lote.** Aggregation key is `medicamento_id`. Sum of `cantidad` from `venta_detalles` joined to ventas where estado = completada and venta date ∈ rango. Order by units desc, limit 10.
- **CSV streamed.** Use Laravel's `response()->streamDownload(...)` so a year of ventas doesn't OOM. Headers written first row, then chunked iteration over the filtered query.

## Defaults

- Rango: first day of current month → today.
- Estado: `completada` (the typical management view).
- Sin método de pago, sin vendedor (todos).

## Non-goals

- No charts/graphs in v1 — KPI cards + tables only.
- No comparisons period-over-period.
- No multi-administrator scoping; single tenant.

## Open questions / risks

- If venta has no `total` column and instead totals come from detalles, KPIs need sum of detalles per venta. Confirm schema before SQL aggregates.
- Method-of-payment as enum vs free text — assume enum aligned with project DBML.
