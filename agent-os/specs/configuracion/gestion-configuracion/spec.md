# Spec: Gestión de Configuración Global

## Purpose

Allows the administrator to adjust operational parameters of the system at runtime without modifying code or restarting the server. Values live in `CatConfiguracion`, are read at request time, and take effect on the next request.

## Actors

Administrator only. No other role has access to read or write configuration.

## Table: CatConfiguracion

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED | Primary key, auto-increment |
| `cClave` | VARCHAR | Unique key identifying the parameter (e.g. `dias_alerta_vencimiento`) |
| `cValor` | VARCHAR | Always stored as string regardless of logical type |
| `eTipoDato` | ENUM(`INTEGER`, `BOOLEAN`, `STRING`, `DECIMAL`) | Controls cast on read and input widget on edit |
| `bEditable` | BOOLEAN | If false, row is visible but cannot be modified through the UI |
| `cDescripcion` | VARCHAR | Human-readable description shown in the table |
| `fActualizado` | TIMESTAMP | Regenerated on every save |

## Dynamic Type Casting

`cValor` is persisted as a plain string. The service layer casts the value to the appropriate PHP type on read, driven by `eTipoDato`:

- `INTEGER` → `(int)`
- `DECIMAL` → `(float)`
- `BOOLEAN` → `filter_var($val, FILTER_VALIDATE_BOOLEAN)`
- `STRING` → raw string

This keeps the storage layer simple while giving callers a typed value.

## Seed Requirements

The following two rows must exist after running the seeder. They are required by the stock-alerts module:

| cClave | eTipoDato | cValor (default) | cDescripcion |
|---|---|---|---|
| `dias_alerta_vencimiento` | INTEGER | `30` | Days before expiry date to trigger an alert |
| `umbral_aviso_stock_bajo` | INTEGER | `0` | Minimum stock units before a low-stock warning fires |

If a consuming module calls `getValue` with a key that does not exist in the table, it receives the hardcoded fallback it passed — no exception is raised.

## No-Cache Pattern

There is no application-level cache for configuration values. Every read queries the database directly. This means:

- A saved change is visible on the very next request.
- No cache flush step is needed on update.
- The table is small (only administrator-editable params), so the query cost is negligible.

## Access Control

Routes are protected by `['auth', 'role:administrator']` middleware. No other role may list or update configuration entries.

## UI Behaviour

The admin sees a table of all rows where any `bEditable` state exists. Each row shows `cClave`, `cDescripcion`, `eTipoDato`, and `cValor`. Rows where `bEditable = false` are displayed read-only (no edit action). For editable rows the input widget matches the type:

- `INTEGER` → `<input type="number" step="1">`
- `DECIMAL` → `<input type="number" step="0.01">`
- `BOOLEAN` → toggle / checkbox
- `STRING` → `<input type="text">`

On save, `cValor` is written as a string and `fActualizado` is set to `now()`.
