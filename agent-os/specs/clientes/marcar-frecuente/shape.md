# Shape Notes: Marcar Cliente Frecuente

## Scope

Narrowest possible change: one boolean field, one endpoint, one shared badge component. No new table, no migration (field assumed present on `clientes`), no confirmation flow.

## Key Decisions

### Single-field update, no form
A PATCH to `/clientes/{id}/frecuente` touches only `bFrecuente`. No general-purpose update endpoint is reused or created. This keeps the blast radius minimal.

### No confirmation dialog
The product spec explicitly says "sin confirmación". Reverting is trivially cheap (tap again), so the UX cost of a dialog is not justified.

### Optimistic UI with rollback
The toggle flips instantly in the browser. If the PATCH fails (network error, server error), Alpine.js restores the prior value and displays an inline error string. No toast library needed — a simple `x-show` message alongside the button is enough.

### JSON response, not redirect
This is the only endpoint in `ClienteController` that returns `JsonResponse`. All others return `View|RedirectResponse`. The deviation is intentional and limited to this method.

### Shared badge component
`<x-ui.badge-frecuente>` is rendered in the catalog table, the detail ficha, and the sale screen. One component, three placements. Avoids duplicated markup.

## Out of Scope

- Bulk-marking multiple clients as frequent.
- Filtering the catalog by `bFrecuente`.
- Any discount logic triggered by the frequent flag (that belongs to a separate feature).
- Audit log of who toggled the flag.

## Risks / Open Questions

- Field name on the `clientes` table: spec uses `bFrecuente` — verify against DBML schema before implementing Task 3.
- If `ClienteController` does not exist yet, it must be created as part of Task 5 (or a prior `catalogo-clientes` task may have created it already — check before adding).
