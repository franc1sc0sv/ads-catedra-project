# FarmaSys — Gap Analysis Report (Functional)

> Read-only audit. Date: 2026-04-30. Branch: `feature/reports-and-auditory`.
> **Pharmacist scope is excluded** (owned by other dev).
> **Architecture is considered done.** Service-layer absence, FormRequests, namespacing, `declare(strict_types=1)`, DBML naming drift, etc. are NOT reported. Only **functional** gaps — "can the user actually do this end-to-end and is the data correct?"

## Executive Summary

- **Specs audited (non-pharmacist):** 9 domains, 26 features.
- **Functional status:** ✅ Working: 4 domains · 🟡 Working with bugs/missing features: 3 · ❌ Not implemented: 2.
- **P0 bugs (block existing flows):** 3
- **P0 missing features:** 4
- **P1 missing features:** 4
- **P2 missing features:** 2

## Per-Domain Breakdown (Functional)

### auth — ✅ Working
- Login, logout, role gating, inactive-user logout all function.
- `last_login_at` is updated.
- **No functional gaps.**

---

### usuarios — ✅ Working
- Admin can list, create, edit, toggle active, reset passwords.
- Self password change works.
- Admin cannot deactivate own account (commit `37b967d`).
- **No functional gaps.**

---

### inventario — ✅ Working
- `catalogo-medicamentos`: list, show, create, edit, soft delete + restore — all wired.
- `alertas-stock`: index page exists.
- `ajuste-stock`: create + store endpoints work.
- `historial-movimientos`: index + per-medication detail work.
- **No functional gaps.**

---

### proveedores — ✅ Working
- `catalogo-proveedores`: full CRUD.
- `crear-pedido`, `listado-pedidos`, `recibir-pedido`: routes + views + cancel/receive flows present.
- `recibir-pedido` is the canonical atomic-transaction reference.
- **No functional gaps.**

---

### ventas — 🟡 Partial | Priority: P0

Can create a sale and mark one CANCELLED. Several functional invariants and one feature are missing.

**Functional bugs:**

1. **Cancel does not restore stock** — `SalesController::cancel()` only flips status to `CANCELLED`. Items decremented from `medications.stock` in `store()` stay decremented forever. Real consequence: cancelling a sale silently destroys stock.
2. **Cancel does not write a `DEVOLUCION` inventory movement** — even setting aside (1), the audit/movement trail will show only the original `SALIDA_VENTA` (which itself isn't written either; see #3).
3. **Sale does not write `INVENTORY_MOVEMENT` rows on creation** — `store()` decrements `medications.stock` directly without inserting a `SALIDA_VENTA` row. Real consequence: `inventario/movimientos` and `reporte-movimientos` will never show ventas-driven stock movements.
4. **Cancel does not capture `cancellation_reason`** — DBML mandates it when status is CANCELADA. `cancel()` accepts no input and does not record why.
5. **`payment_method` validation only allows `'cash'`** (`SalesController.php` validation rule `'required|in:cash'`). User cannot pay by card / transfer even though the enum and DBML support it.

**Missing features:**

6. **`adjuntar-receta` not implemented** — no endpoint, no UI hook, no write path to `sale_prescriptions`. Salesperson cannot attach a prescription to a sale containing a controlled medication. Spec exists (`agent-os/specs/ventas/adjuntar-receta/spec.md`).

**Suggested fix order:**
1. Add `cancellation_reason` capture to `cancel()` form + endpoint.
2. Inside the existing `store()` transaction, write one `InventoryMovement` (`SALIDA_VENTA`) per item.
3. Inside `cancel()`, restock items and write `DEVOLUCION` movements (or reverse `SALIDA_VENTA`s) atomically.
4. Expand payment method validation against the full enum.
5. Implement `adjuntar-receta` (depends on `recetas/registro-receta` being live — see below).

---

### clientes — 🟡 Partial | Priority: P0 (live bugs)

**Functional bugs (both currently in production code):**

1. **Admin role is silently rejected from all clientes routes.** `routes/web/clientes.php` uses `role:admin,salesperson`. The `UserRole` enum has no `admin` value — only `administrator`. `EnsureRole` does an exact-match check, so any logged-in administrator gets `403` for the entire `clientes` resource (index/create/edit/show + `/historial`). Recent commit `b0882b3` ("Solo Admin") relies on admins being able to reach `destroy` — they currently can't even reach the index.
2. **`destroy` denies all users.** `CustomerController::destroy` checks `$user->role !== 'admin'`. `User::role` is cast to the `UserRole` enum, so the comparison is enum-vs-string and never matches. Result: nobody can delete a customer through the UI.

**Functional features status:**

- ✅ `catalogo-clientes` (list / create / edit) works for salesperson.
- ✅ `marcar-frecuente` works via the `is_frequent` checkbox on the form.
- 🟡 `historial-compras` partial — `show()` eager-loads only the latest 10 sales for a customer. If spec wants paginated full history, that needs widening.
- 🟡 `busqueda-venta` partial — text search works on the `clientes` index page, but there is no JSON / autocomplete endpoint that the POS create-sale view can call to look up a customer mid-sale (spec implies in-POS lookup).

**Suggested fix order:**
1. Change middleware to `role:administrator,salesperson`.
2. Fix destroy guard to compare against `UserRole::ADMINISTRATOR` (or move admin-only deletion into a separate route group with `role:administrator`).
3. Add JSON search endpoint for POS autocomplete.
4. Widen / paginate purchase history if spec demands.

---

### recetas (registro-receta only — pharmacist features excluded) — ❌ Not implemented | Priority: P0

- `routes/web/recetas.php` is an empty placeholder.
- The POS sale flow (`SalesController::store`) does not detect controlled medications and does not create `Prescription` rows or `SalePrescription` links.
- Salesperson has no UI to capture prescription number, patient name, doctor data when selling a controlled drug.
- **This blocks the pharmacist's `cola-pendientes` queue downstream** — that queue has no rows to read until salesperson POS writes them.

**Suggested:** Add a "Adjuntar receta" step inside the sale create view, write `Prescription` + `SalePrescription` atomically inside the `store()` transaction when any item flagged controlled is in the cart.

---

### reportes — ❌ Not implemented | Priority: P1

`routes/web/reportes.php` is an empty placeholder. None of the four features have routes, controllers, or views:

- `reporte-ventas` — no endpoint, no UI.
- `reporte-inventario` — no endpoint, no UI.
- `reporte-movimientos` — no endpoint, no UI.
- `bitacora-auditoria` — no endpoint, no UI. **And `audit_logs` is never written by any current flow** (no observers, no event listeners, no explicit inserts in usuarios/inventario/ventas/proveedores). Even if a viewer were built today, it would render an empty table.

**Suggested fix order:**
1. Wire audit writes first (login/logout, ventas create/cancel, ajuste-stock, recibir-pedido, usuario create/toggle). Without this, the bitácora is permanently empty.
2. Build the three operational reports (ventas / inventario / movimientos) with date-range filters.
3. Build bitácora viewer.

---

### configuracion — ❌ Not implemented | Priority: P2

- `Setting` model + `settings` migration + `SettingSeeder` are present and seed initial values, so the data is available for other code to read.
- `routes/web/configuracion.php` is an empty placeholder. **Admin has no UI to view or change settings at runtime.** The single feature `gestion-configuracion` is not functional end-to-end.
- No nav link in `components/nav/administrator-nav.blade.php`.

**Suggested:** Build admin index/edit page with type-aware widgets (INTEGER → number, BOOLEAN → toggle, etc.), gate routes with `role:administrator`, add admin nav link.

---

## Recommended Functional Fix Order

1. **Fix `clientes` middleware + destroy guard (P0, ~15 min).** Two one-liners. Currently admin is locked out and nobody can delete.
2. **Fix `ventas` cancel data integrity (P0).** Cancel must capture motivo, restock items, and write reverse movements inside a transaction. Without this, cancelling silently breaks stock.
3. **Wire `InventoryMovement` writes in `ventas` store/cancel (P0).** Required so movement history and reports are not silently empty for sales activity.
4. **Implement `recetas/registro-receta` from POS (P0).** Unblocks pharmacist queue downstream.
5. **Wire audit-log writes across already-implemented domains (P1).** Otherwise `bitacora-auditoria` is permanently empty.
6. **Implement `reportes` (P1)** — 3 operational reports + bitácora viewer.
7. **Implement `ventas/adjuntar-receta` (P1)** — depends on (4).
8. **Implement `clientes/busqueda-venta` JSON endpoint + widen `historial-compras` (P1/P2).**
9. **Expand `ventas` payment-method options (P2).**
10. **Implement `configuracion/gestion-configuracion` admin UI (P2).**

---

## Excluded from this report
- `pharmacist` role: dashboard view, `cola-pendientes`, `validar-rechazar`, `historial-recetas`, `pharmacist-nav.blade.php` updates.
- Architecture-only critiques (service interfaces, FormRequests, controller namespacing, `strict_types`, DBML-vs-Eloquent naming, `try/catch` style). These are considered done per scope clarification.
