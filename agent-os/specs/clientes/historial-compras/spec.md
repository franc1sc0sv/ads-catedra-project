# Spec: historial-compras

## Overview

The purchase history view is a read-only, chronological list of all sales associated with a specific client. It lives inside the client detail page and answers "what has this person bought and when?". Accessible to salespersons and administrators.

## Behavior

When a user opens a client's record, a section renders the purchase history as a paginated table ordered from newest to oldest. Each row shows: date, total amount, payment method, and status (completed or cancelled). Cancelled sales are visually differentiated with a badge or strikethrough indicator so staff can quickly distinguish them from valid purchases.

Clicking a row navigates to the full sale detail view, which loads the associated `DetalleVenta` records via eager-loading at that point — detail is not pre-loaded in the list query to keep the paginated response lean.

If the client has no recorded sales, the table area is replaced with an empty-state message (e.g. "Este cliente no tiene ventas registradas.").

## Pagination

Server-side pagination via Laravel's `LengthAwarePaginator`. The default page size is 15 rows. Pagination controls appear below the table.

## Data Model Assumptions

- A `Venta` belongs to a `Cliente`.
- `Venta` has `fecha`, `total`, `metodo_pago`, and `estado` (enum with at least `completada` and `cancelada` values).
- `DetalleVenta` belongs to `Venta` and holds the line items (product, quantity, price).
- Eager-loading of `DetalleVenta` happens on the sale detail route, not the history list route.

## Routing

| Method | URI | Controller | Description |
|---|---|---|---|
| GET | `/clientes/{cliente}/historial` | `ClienteController@historial` | Paginated history list |
| GET | `/ventas/{venta}` | Existing or new sale detail controller | Sale detail (loads `detalleVentas`) |

## Access Control

Routes are protected with `['auth', 'role:salesperson,administrator']`.

## Views

- `resources/views/salesperson/clientes/historial.blade.php`
- `resources/views/admin/clientes/historial.blade.php`

Both views share the same Blade logic; the role-namespaced separation follows the project convention.
