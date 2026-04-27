# Shape Notes: Alertas de Stock

## Core Bets

**Config-driven thresholds, not hardcoded.**
Both the stock-low offset (`umbral_aviso_stock_bajo`) and the expiry window (`dias_alerta_vencimiento`) live in `CatConfiguracion`. The service reads them at query time via `ConfiguracionService`. No magic numbers in PHP. Changing thresholds is a data operation, not a deploy.

**No real-time. Page-refresh is enough.**
Alerts are operational, not critical-safety. A pharmacist finding out about low stock when they open the dashboard — rather than via a push notification — matches the actual workflow. Adding WebSockets or polling is scope creep for an MVP.

**A product can be in both blocks.**
A near-expired item is also often understocked (returns, holds). Deduplicating would hide real problems. The two blocks solve different decisions (reorder vs. write-off); showing an item in both is correct.

**Links bridge to other flows, not inline actions.**
The dashboard is read-only. It surfaces the problem; the action happens in crear-pedido or ajuste-stock. This keeps the dashboard controller thin and avoids duplicating write logic.

## Boundaries / Rabbit Holes to Avoid

- Do not add email/SMS notifications in this iteration. Alerts = dashboard only.
- Do not build a "dismiss alert" feature. Items leave the list when the underlying data changes (stock replenished, item written off).
- Do not add date-range filters or search to the dashboard. Two sorted lists are sufficient for MVP.
- Do not implement pagination unless the product catalog exceeds a few hundred rows. `->get()` is fine for now.
- The administrator's view is identical to the inventory manager's view. No read-only mode toggle; the action links simply lead to routes the admin cannot POST to.

## Open Questions Resolved

- **Which table holds thresholds?** `CatConfiguracion` key-value table, read via `ConfiguracionService`.
- **Urgency ordering for Block 1?** Lowest `nStockActual / nStockMinimo` ratio → most urgent first. This naturally floats out-of-stock items to the top.
- **Time zone for expiry comparison?** Use `now()` in app timezone (configured in `config/app.php`). `fVencimiento` is a date column; compare with `Carbon::today()` and `Carbon::today()->addDays($dias)`.
