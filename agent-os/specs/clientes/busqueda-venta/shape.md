# Shape Notes: busqueda-venta

## Scope

Inline client search and quick-create during new-sale flow. Does not cover client listing, editing, deactivation, or reactivation. Those are separate features.

## Key Decisions

### LIKE search, max 10 results
Full-text search is overkill for an MVP with a small client base. `ILIKE '%query%'` on `nombre` and `identificacion` is simple, readable, and sufficient. Result cap at 10 keeps the dropdown usable without scrolling.

### Modal, not redirect
The cashier is mid-sale. A redirect to a client creation page would discard cart state. The modal approach preserves the sale context entirely. Alpine.js handles the modal state on the frontend; no extra route or controller action is needed beyond the JSON endpoint.

### Cart preserved on modal close
Closing the modal (ESC, backdrop click, cancel button) must not affect the cart. The modal is layered on top — it owns no cart state. This is guaranteed by keeping the modal entirely within the sale Blade component.

### Duplicate check covers deactivated clients
A deactivated client with the same `identificacion` could cause confusion if silently recreated. The check intentionally queries all rows regardless of `activo`. The cashier sees a warning and can escalate to an admin for reactivation.

### frecuente badge is read-only here
The badge surfaces existing data. Setting a client as frequent is an admin/manager operation outside this feature.

### JSON responses for search and quick-create
Both endpoints return JSON because the interactions are driven by Alpine.js fetch calls. No full page reload occurs.

## Standards Applied

- `authentication/role-middleware` — routes guarded by `salesperson,administrator`.
- `backend/php-architecture` — Route → FormRequest → Controller → ServiceInterface → Service → Model.
- `backend/service-interface` — controller injects interface only.
- `frontend/role-namespacing` — component embedded in `salesperson/ventas/` view tree; shared `x-ui.*` components reused.
