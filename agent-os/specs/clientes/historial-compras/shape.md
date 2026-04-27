# Shape: historial-compras

## Scope

Read-only. No create, edit, or delete actions on this screen. The only mutation surface is navigating away to the sale detail.

## Pagination strategy

Server-side via `LengthAwarePaginator`. Do not use infinite scroll or client-side filtering — keep it simple and crawlable. Default page size: 15.

## Eager-loading boundary

The history list query fetches only `Venta` columns — no joins to `DetalleVenta`. Detail records are expensive to load in bulk. They are loaded lazily (via `$venta->load(...)`) only when the user navigates to a single sale's detail route. This is the explicit split:

- `GET /clientes/{cliente}/historial` — paginated `Venta` rows, no detail
- `GET /ventas/{venta}` — single `Venta` with `detalleVentas` eager-loaded

## Cancelled sales indicator

Use a Tailwind badge component on the status column. Suggested approach:

```blade
@if($venta->estado->value === 'cancelada')
    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
        Cancelada
    </span>
@else
    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
        Completada
    </span>
@endif
```

Do not hide cancelled rows — they must remain visible for audit purposes.

## Row navigation

Wrap each `<tr>` with an `onclick` or wrap the primary cell in an `<a>` tag pointing to `route('ventas.show', $venta)`. Avoid full-row anchor wrapping (invalid HTML) — use a dedicated "Ver" link cell instead.

## Empty state

When `$ventas->isEmpty()`, render a centered message inside the table body area:

```blade
<tr>
    <td colspan="4" class="text-center py-8 text-gray-500">
        Este cliente no tiene ventas registradas.
    </td>
</tr>
```

## Out of scope

- Search/filter within the history (not in MVP)
- Export to CSV/PDF
- Date range picker
- Real-time updates
