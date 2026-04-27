# Spec: Catálogo de Clientes

## Overview

The customer catalog is the central registry for all pharmacy clients. It provides full CRUD operations on the `Cliente` model, with soft-delete support, unique identification enforcement, and a locked-identification rule when sales history exists.

## Cliente Model

Attributes:

- `id` — primary key
- `nombre` — full name, required
- `telefono` — phone number, nullable
- `correo` — email address, nullable
- `direccion` — address, nullable
- `identificacion` — national ID (DUI), required, unique across the entire table (including soft-deleted records at DB level)
- `es_frecuente` — boolean flag for frequent/loyalty customers, default false
- `bActivo` — boolean soft-delete flag, default true; set to false on deactivation instead of using Laravel's `deleted_at`
- `timestamps` — `created_at`, `updated_at`

## Unique Identification Constraint

The `identificacion` column has a unique database index. On creation, if the value already exists (whether the existing record is active or not), the system returns a validation error. The `CreateClienteRequest` enforces this with a `unique:clientes,identificacion` rule.

## Identification Lock When Ventas Exist

On update, `ClienteService::update()` checks whether the cliente has any associated `Venta` records. If ventas exist, the `identificacion` field is excluded from the update payload and the `UpdateClienteRequest` marks it as not required (or the service silently drops it). This prevents orphaned sales records.

## Soft Delete Pattern

This feature uses an `bActivo` boolean column rather than Laravel's `SoftDeletes` trait. Rationale: the `bActivo` flag maps naturally to the domain language already used in the schema (other tables use the same convention), and it avoids the need for global scope overrides in queries.

- `destroy` action: sets `bActivo = false`
- `restore` action: sets `bActivo = true`
- The default index query filters `WHERE bActivo = true`
- Search in the sale flow also filters `WHERE bActivo = true`
- History/admin views can query all records regardless of `bActivo`

## Search

The index action accepts an optional `q` query parameter. When present, the service applies `WHERE nombre LIKE %q% OR identificacion LIKE %q%`. Filtering is case-insensitive (handled at the DB collation level or via `ILIKE` on PostgreSQL). Results are paginated (15 per page).

## Authorization

Both `salesperson` and `administrator` roles have access to all actions (index, create, store, edit, update, destroy, restore). Routes are wrapped in `role:salesperson,administrator` middleware.

## Controller Actions

- `index` — paginated list of active clients, optional search
- `create` — show create form
- `store` — validate via `CreateClienteRequest`, call service, redirect to index
- `edit` — show edit form for a specific client
- `update` — validate via `UpdateClienteRequest`, call service, redirect to index
- `destroy` — soft-delete (set `bActivo = false`), redirect to index
- `restore` — reactivate (set `bActivo = true`), redirect to index

## Views

Both roles share the same `ClienteController`. The controller resolves the view path based on the authenticated user's role:

- `salesperson/clientes/index`, `salesperson/clientes/create`, `salesperson/clientes/edit`
- `admin/clientes/index`, `admin/clientes/create`, `admin/clientes/edit`

Shared UI components (`ui.button`, `ui.card`, `ui.input`) are used in all views.
