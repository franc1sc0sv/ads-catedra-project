# Tasks — Historial de Movimientos

- [x] **Task 1: Save spec documentation** — `spec.md`, `shape.md`, `standards.md`, `references.md` creados en este folder.

- [x] **Task 2: Service contract**
  - Crear `app/Services/Inventario/Contracts/MovimientoServiceInterface.php`.
  - Método `getByMedicamento(int $medicamentoId, array $filters = [], int $perPage = 25): LengthAwarePaginator`.
  - `$filters` admite `desde`, `hasta`, `tipos[]`.

- [x] **Task 3: Service implementation**
  - Crear `app/Services/Inventario/MovimientoService.php` con `declare(strict_types=1)` y constructor readonly.
  - Implementar `getByMedicamento`: filtra por medicamento, aplica rango de fechas y tipos, eager-load `venta`, `pedido`, `usuario`, ordena `created_at DESC`, pagina.
  - Registrar binding `MovimientoServiceInterface => MovimientoService` en `AppServiceProvider`.

- [x] **Task 4: Controller**
  - Crear `app/Http/Controllers/Web/Inventario/MovimientoController.php`.
  - Inyectar `MovimientoServiceInterface` por constructor readonly.
  - Action `index(Request, Medicamento)`: valida filtros, llama al servicio, retorna `View|RedirectResponse`.
  - La vista a renderizar depende del rol (`admin/inventario/movimientos` vs `inventory-manager/inventario/movimientos`).

- [x] **Task 5: Routing**
  - En `routes/web.php`, agrupar bajo `auth` + `role:inventory_manager,administrator`.
  - Ruta: `GET /inventario/medicamentos/{medicamento}/movimientos` → `MovimientoController@index` (name `inventario.movimientos.index`).

- [x] **Task 6: Vistas por rol**
  - `resources/views/inventory-manager/inventario/movimientos.blade.php`.
  - `resources/views/admin/inventario/movimientos.blade.php`.
  - Tabla cronológica: fecha, tipo, cantidad, stock antes/después, usuario, link al origen (venta/pedido) cuando exista.
  - Form de filtros (fecha desde/hasta, tipos) que preserva query en la paginación.
  - Enlace de retorno al detalle del medicamento.

- [x] **Task 7: Verificación manual**
  - Login como `inventory@pharma.test` y `admin@pharma.test`.
  - Confirmar acceso, filtros funcionando, links a venta/pedido, paginación.
  - Confirmar que `salesperson` y `pharmacist` reciben 403.
