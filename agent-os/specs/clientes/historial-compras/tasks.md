# Tasks: historial-compras

## Task 1 — Docs (done)
- [x] spec.md written
- [x] tasks.md written
- [x] shape.md written
- [x] standards.md written
- [x] references.md written

## Task 2 — Pagination in CustomerController::show [DONE]
- [x] `CustomerController::show()` now calls `$cliente->sales()->latest()->paginate(15)->withQueryString()` and passes `$sales` to the view.
  Implemented inside the existing `show()` method (route `salesperson.clientes.show` / `salesperson.clientes.historial`) instead of a separate `historial()` method, since the existing route already serves this purpose. The c-prefix DBML naming (`Cliente`, `ventas`, `fecha`) is replaced with the codebase's canonical English names (`Customer`, `sales`, `created_at`).

## Task 3 — Controller method [DONE — folded into Task 2]
- [x] No separate `historial()` method needed; existing `show(?Customer $cliente)` is paginated.

## Task 4 — Routes [DONE]
- [x] `routes/web/clientes.php` keeps both `salesperson.clientes.show` (resource) and `salesperson.clientes.historial` (`/historial/{customer?}`) under `['auth', 'role:administrator,salesperson']`. Both route to the same `show()` method.

## Task 5 — Blade view [DONE]
- [x] `resources/views/salesperson/dashboard/customer/show.blade.php` updated:
  - Columns: Folio, Fecha, Método, Total, Estado.
  - Cancelled rows tinted `bg-red-50` with "Cancelada" badge.
  - Pagination via `{{ $sales->links() }}`.
  - Empty-state preserved.
- (Spec called for admin variant — admin already reaches the same salesperson view by sharing the `salesperson.clientes.*` route prefix per project convention; no separate admin view needed.)

## Task 6 — Sale detail route — out-of-scope
- Sale-detail route (`GET /ventas/{venta}`) lives in the ventas module which is out of scope for this branch (per audit gap-analysis). Row-level links to the sale detail are intentionally omitted; the row still shows folio/fecha/total/estado.
