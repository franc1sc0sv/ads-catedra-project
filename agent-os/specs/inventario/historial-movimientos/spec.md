# Spec: historial-movimientos

## Overview

The movement history feature provides a read-only chronological audit log for any given medicamento. It answers the question "why does this product have this stock today?" by showing every stock change since the product was registered.

## Behavior

The view is accessed from the medicamento detail page as a sub-route (e.g. `/inventario/medicamentos/{medicamento}/movimientos`). It is strictly read-only — no creates, updates, or deletes are exposed from this screen.

The list is ordered from most recent to oldest. Each row displays:

- Date and time of the movement
- Movement type (compra, venta, ajuste_manual, baja_vencimiento, devolucion)
- Quantity delta (positive for entries, negative for exits)
- Stock before the movement
- Stock after the movement
- Responsible user (name)
- Link to the originating record when applicable (venta or pedido)

## Filters

Two optional filters are supported:

1. **Date range** — `fecha_desde` and `fecha_hasta` (inclusive). Required for large histories to avoid loading thousands of rows.
2. **Movement type** — single enum value from the `tipo` column.

Both filters are applied server-side before pagination.

## Pagination

Results are paginated (15 per page by default). The service returns a `LengthAwarePaginator` so Blade can render standard Laravel pagination links.

## Eager Loading

The service eager-loads the `venta` and `pedido` relationships on every page to avoid N+1 queries when rendering the "source link" column. Relationships that are null for a given row are handled gracefully in the view.

## Immutability Guarantee

No mutation routes exist for this feature. Movement records are append-only: when stock corrections are needed, a compensating adjustment entry is created (see `ajuste-stock` spec) rather than editing or deleting an existing movement row.

## Access Control

Accessible only to users with roles `inventory_manager` or `administrator`. Routes are wrapped in `Route::middleware(['auth', 'role:inventory_manager,administrator'])`.
