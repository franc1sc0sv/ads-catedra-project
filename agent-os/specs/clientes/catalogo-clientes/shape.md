# Shape Notes: Catálogo de Clientes

## Scope

In scope:
- CRUD (index, create, edit) for the `Cliente` model
- Unique `identificacion` enforcement at DB and validation layer
- Soft-delete via `bActivo` boolean (deactivate / restore)
- Identification lock when the client has associated ventas
- Search by `nombre` or `identificacion` on the active-records index
- Role-namespaced views for `salesperson` and `administrator`

Out of scope for this feature:
- Venta (sales) creation — handled in a separate feature
- Descuentos / loyalty rewards logic — separate feature referencing `es_frecuente`
- Admin-only history view of inactive clients (can be added as a follow-up sub-spec)

## Key Decisions

### Soft Delete via `bActivo` Flag
Using a `bActivo` boolean column instead of Laravel's `SoftDeletes` trait (`deleted_at`). This matches the domain convention used elsewhere in the schema. It avoids needing global query scope overrides and maps directly to the business language ("activo / inactivo"). The tradeoff is that standard Eloquent `SoftDeletes` helpers (`withTrashed`, `onlyTrashed`, `restore()`) are not available — the service handles this explicitly.

### Unique Identificacion (Including Inactive Records)
The unique constraint lives at the database level and is not scoped to `bActivo = true`. A reactivated client retains their original identificacion. If a truly new person shares a previously used DUI, the admin must first restore (or permanently delete) the old record before creating a new one. This is the safest default for a pharmacy with legal record-keeping requirements.

### Identification Lock When Ventas Exist
The `ClienteService::update()` method checks `$cliente->ventas()->exists()` and drops `identificacion` from the update payload if true. This is a service-layer guard, not a database constraint. The `UpdateClienteRequest` still accepts `identificacion` (to avoid confusing frontend forms), but the service silently ignores it in this case. The edit view should visually disable the field when `$hasVentas` is true.

### Shared Controller, Dual View Paths
One `ClienteController` serves both roles. The controller uses a `resolveView(string $name)` helper that applies `match(auth()->user()->role->value)` to select `salesperson/clientes/{name}` or `admin/clientes/{name}`. This avoids duplicating controller logic while respecting the project's role-namespacing convention.

### No `show` Route
The spec does not require a detail page; `show` is excluded from the resource route.

## Standards Applied

- `authentication/role-middleware`: routes under `['auth', 'role:salesperson,administrator']`
- `backend/php-architecture`: Route → FormRequest → Controller → ServiceInterface → Service → Model
- `backend/service-interface`: controller injects interface, service holds business logic
- `frontend/role-namespacing`: separate view files per role under `salesperson/clientes/` and `admin/clientes/`
