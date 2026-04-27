# Shape Notes: Gestión de Configuración Global

## Key Design Decisions

### cValor always stored as string

Every value — regardless of logical type — is persisted as a VARCHAR string. The service layer is the single place responsible for casting to the correct PHP type on read. This avoids a polymorphic column design and keeps migrations simple. Downstream modules receive a typed value; they never touch the raw string.

### Cast on read, driven by eTipoDato

The `eTipoDato` enum drives both:
1. The PHP type returned by `getValue` (via a `match` expression in the service).
2. The HTML input widget rendered in the Blade view.

Neither the controller nor the model performs any casting. The service owns this responsibility entirely.

### bEditable gate

`bEditable = false` rows are intentionally exposed in the UI as read-only reference data. The service's `update` method must check this flag and raise an exception (or silently no-op — implementer's choice, but raising is safer) if an update is attempted on a non-editable key. This prevents a malicious or mistaken PATCH request from modifying system-locked keys.

### Seed required — not optional

The two MVP keys (`dias_alerta_vencimiento`, `umbral_aviso_stock_bajo`) must be created by the seeder. The stock-alerts module reads them via `getValue` with a fallback default, so the system won't break if they're missing, but the admin UI will show an empty configuration table which is a bad experience. The seeder should use `upsert` / `firstOrCreate` so re-running it is idempotent.

### No cache invalidation needed

There is no cache layer for config values. Reads are direct DB queries. The table will always be small (a handful of rows), so this is acceptable. Adding a cache later is a straightforward refactor if needed.

### No reinicio / no deploy needed

Changes take effect on the next HTTP request. The admin saves a value, the next module request reads the new value from the DB. No queue flush, no server restart, no config:cache call.

### Admin-only, no API surface

This feature is web-only. There is no JSON API endpoint. The controller returns `View|RedirectResponse` exclusively. Authentication and authorization are enforced entirely via route middleware — the controller does not check roles.
