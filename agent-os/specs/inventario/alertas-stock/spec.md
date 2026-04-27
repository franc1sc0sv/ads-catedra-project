# Spec: Alertas de Stock

## Overview

The stock alerts dashboard gives the inventory manager (and the administrator in read-only mode) an at-a-glance view of items that need immediate attention: products whose current stock has fallen below the configured minimum, and products whose expiry date is approaching. The page loads fresh on every visit — there is no real-time push or polling.

## Two-Block Layout

### Block 1 — Bajo Mínimo (Below Minimum)

Displays every `Medicamento` whose `nStockActual` is less than `nStockMinimo` plus the configurable sensitivity offset `umbral_aviso_stock_bajo` (read from `CatConfiguracion`). Default offset is 0, meaning exact threshold; a positive value widens the warning window so items are flagged before they actually hit the floor. Results are ordered by urgency: lowest `nStockActual / nStockMinimo` ratio first.

Each row shows: product name, current stock, minimum stock, and a direct link to create a replenishment order (crear-pedido flow).

### Block 2 — Próximos a Vencer (Expiring Soon)

Displays every `Medicamento` whose `fVencimiento` falls within the next N days, where N is `dias_alerta_vencimiento` from `CatConfiguracion` (default 30). Results are ordered by soonest expiry date first.

Each row shows: product name, batch/lot if available, expiry date, current stock, and a direct link to register a write-off via the ajuste-stock (baja por vencimiento) flow.

## Cross-Block Membership

A product can appear in both blocks simultaneously — a nearly-expired item that is also understocked is shown in both. The dashboard does not deduplicate across blocks.

## Configuration Source

Both thresholds are read from the `CatConfiguracion` table (key-value store). The service resolves them via `ConfiguracionService` so that changes to thresholds take effect on the next page load without a code deploy.

| Key | Default | Purpose |
|---|---|---|
| `umbral_aviso_stock_bajo` | 0 | Extra units added to nStockMinimo before comparison |
| `dias_alerta_vencimiento` | 30 | Look-ahead window in days for expiry warnings |

## Roles

| Role | Access |
|---|---|
| `inventory_manager` | Full access, primary user |
| `administrator` | Read-only view (same dashboard, no action links hidden — links lead to role-protected routes) |

## Load Pattern

The dashboard is server-rendered on page load. No AJAX, no WebSocket, no polling. Refreshing the browser fetches fresh data.

## Navigation Links

- "Crear pedido" links from Block 1 rows navigate to the crear-pedido form pre-populated with the product.
- "Registrar baja" links from Block 2 rows navigate to the ajuste-stock form pre-filled for a vencimiento write-off.
