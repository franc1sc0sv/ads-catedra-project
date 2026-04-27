# Shape — Ajuste de Stock

## Scope

- Corrección puntual de stock por un solo medicamento a la vez.
- Tres tipos de movimiento: `ajuste_manual`, `baja_vencimiento`, `devolucion`.
- Registro inmutable en `MovimientoInventario`.
- Acceso exclusivo del rol `inventory_manager`.

## Out of scope

- Ajustes en lote (múltiples medicamentos en una operación).
- Edición o eliminación de movimientos.
- Importación CSV.
- Notificaciones a otros roles.
- Workflow de aprobación.
- Visualización de historial (spec separada).
- Movimientos que originan ventas o pedidos (otros flujos los generan).

## Key Decisions

- **Atomic transaction.** Actualización de stock y creación de movimiento ocurren dentro de un solo `DB::transaction(...)`. No es admisible un stock cambiado sin movimiento asociado, ni un movimiento sin cambio de stock. Estado parcial corrompe la auditoría.
- **Immutable records.** `MovimientoInventario` no se edita ni se borra (no `softDeletes`, no update route). La auditoría depende de esta propiedad.
- **Compensating adjustment pattern.** Una corrección errónea se enmienda con un nuevo movimiento en sentido contrario. Mantiene el rastro completo y evita pérdida de información histórica.
- **Stock values captured inside transaction.** `nStockAntes` se lee dentro de la transacción abierta para evitar race conditions entre ajustes concurrentes.
- **Negative quantity allowed.** `cantidad` puede ser positiva o negativa (refleja la dirección del ajuste). `cantidad = 0` no se acepta.
- **Stock floor guard.** El servicio rechaza ajustes que dejarían el stock en negativo (regla de dominio, no de validación de input). Se enforce en el servicio porque requiere leer el stock actual.
- **Service interface pattern.** `StockServiceInterface` inyectada al controlador; nunca se inyecta la clase concreta.
- **Role enforcement at the route layer.** `role:inventory_manager` middleware. El controlador no chequea rol.

## Context

- **Visuals:** None provided. UI sigue patrón estándar de Blade + Tailwind v4 + Alpine ya establecido en el módulo de auth de referencia.
- **References:**
  - `app/Http/Controllers/Web/Auth/AuthController.php` — patrón de controlador delgado con `readonly` constructor inyectando interface.
  - `app/Services/Auth/Contracts/AuthServiceInterface.php` — patrón de interface en `Contracts/`.
  - `app/Services/Auth/AuthService.php` — patrón de implementación de servicio (ignorar lo de JWT, es legado).
- **Product alignment:** MVP Sección 3 — Inventario. Esta feature es la única vía oficial para corregir discrepancias fuera de los flujos de ventas y pedidos.

## Standards Applied

- `authentication/role-middleware`
- `authentication/session-auth`
- `backend/php-architecture`
- `backend/service-interface`
- `frontend/role-namespacing`

Detalles completos en `standards.md`.
