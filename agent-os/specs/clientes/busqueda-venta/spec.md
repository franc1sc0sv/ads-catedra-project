# Spec: Búsqueda de Cliente en Venta

## Purpose

The client search feature is embedded directly in the new-sale screen. It allows the cashier to find and associate a customer to the current ticket without leaving the sale flow. Keeping the search inline — rather than in a separate tab or page — is a deliberate UX decision: abandoning the sale screen would risk losing cart state and would slow down checkout.

## Search Behavior

As the cashier types in the search field, the system queries active clients whose `nombre` or `identificacion` columns contain the typed string (case-insensitive LIKE). Results are shown in a dropdown list limited to 10 entries to keep the UI scannable without scrolling. Only clients with `activo = true` are included; deactivated clients are silently excluded from suggestions.

When the cashier selects a client from the dropdown, the client is associated with the in-progress sale and a "frecuente" badge appears next to the name if the client is flagged as frequent (`frecuente = true`). The badge is purely informational — it signals to the cashier that personalized attention may be warranted.

## Quick-Create Modal

If the search returns no results, a "Crear cliente" option appears at the bottom of the dropdown. Clicking it opens a modal overlay without closing or resetting the cart. The modal collects the minimum required fields: full name (`nombre`), phone number (`telefono`), and national ID (`identificacion`).

On submit, the system first checks whether any client record — active or inactive — already has the same `identificacion`. If a duplicate exists, the modal displays an inline warning instead of saving, preventing duplicate records in the catalog. This check covers deactivated clients intentionally: reactivation (if needed) is a separate admin operation, not handled here.

If no duplicate is found, the new client is saved permanently to the `clientes` table and immediately associated with the current sale. The modal closes and the sale screen reflects the newly created client.

## Catalog Persistence

Clients created via the quick-create modal are full catalog records, not ephemeral or sale-scoped entries. They appear in future searches and in the full client management section once that feature is built.

## Out of Scope

- Editing or deactivating clients (separate feature).
- Reactivating a deactivated client from within the sale flow.
- Searching by email or address fields.
- Pagination of search results (max 10 is sufficient for the typeahead pattern).
