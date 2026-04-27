# Tasks — Bitácora de Auditoría

- [x] **Task 1:** Save spec documentation (spec.md, shape.md, standards.md, references.md).
- [ ] **Task 2:** Create `AuditoriaAcceso` model + migration (append-only, `cveUsuario` nullable FK, `cDetalles` JSON cast, `fCreado` timestamp, no `fActualizado`).
- [ ] **Task 3:** Create `App\Services\Bitacora\Contracts\BitacoraServiceInterface` with `log(string $accion, ?int $cveUsuario, ?string $tabla, ?string $registro, array $detalles): void` and `getFiltered(array $filters): LengthAwarePaginator`.
- [ ] **Task 4:** Implement `App\Services\Bitacora\BitacoraService`; register binding in `AppServiceProvider`.
- [ ] **Task 5:** Inject `BitacoraServiceInterface` into `AuthController` and log `LOGIN_OK` / `LOGIN_FAIL` (capturing attempted email when user not found) / `LOGOUT`.
- [ ] **Task 6:** Inject `BitacoraServiceInterface` into `RecetaController`, `StockController`, `VentaController`, `UserController` and call `log()` at action points (`RECETA_VALIDADA`, `RECETA_RECHAZADA`, `AJUSTE_STOCK`, `VENTA_CANCELADA`, `USUARIO_CREADO`, `ROL_CAMBIADO`).
- [ ] **Task 7:** Create `app/Http/Controllers/Web/Reportes/BitacoraController.php` with `index(Request): View` only — no `update`/`destroy`.
- [ ] **Task 8:** Register route in `routes/web.php` under `auth` + `role:administrator` middleware: only `GET /admin/reportes/bitacora` → `BitacoraController@index`.
- [ ] **Task 9:** Build `resources/views/admin/reportes/bitacora.blade.php`: filter form (usuario / acción / tabla / fechas), paginated table, default last 24h.
- [ ] **Task 10:** Verify 403 for non-admin roles (manual check or feature test).
