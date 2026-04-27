# Spec: Marcar Cliente Frecuente

## Overview

A single-field toggle that flips the `bFrecuente` boolean on a `Cliente` record. Available to salespersons and administrators. No confirmation dialog — the change persists immediately via a PATCH request.

## Endpoint

`PATCH /clientes/{cliente}/frecuente`

The route is protected by `['auth', 'role:salesperson,administrator']` middleware. The controller method `ClienteController@toggleFrecuente` delegates to `ClienteServiceInterface::toggleFrecuente(Cliente $cliente): Cliente`, which simply inverts the current `bFrecuente` value and saves the model. The response is JSON so Alpine.js can confirm the new state without a full page reload.

## UI Behaviour

The toggle button (or icon switch) appears in two places:

- The row actions column of the clients catalog table (`salesperson/clientes/index.blade.php` and `admin/clientes/index.blade.php`).
- The client detail view.

When the sale screen has a client selected, a badge reading "Frecuente" is shown next to the client's name if `bFrecuente` is true.

### Optimistic Update

Alpine.js flips the local `frecuente` data property immediately on click, then fires the PATCH. If the server returns a non-2xx response the property is rolled back to its original value and a brief inline error message is shown. No full page refresh occurs on success or failure.

## Badge Visibility

The "Frecuente" badge appears in:

1. The `bFrecuente` column of the catalog table.
2. The client detail/ficha header.
3. The selected-client section of the sale screen.

The badge is a shared Blade component (`components/ui/badge-frecuente.blade.php`) so it renders consistently across all three surfaces.

## Constraints

- `declare(strict_types=1)` on every PHP file.
- Readonly constructor promotion for injected services.
- No JWT, no API route — web-only session auth.
- Controller returns `JsonResponse` only for this endpoint; all other client controller methods return `View|RedirectResponse`.
