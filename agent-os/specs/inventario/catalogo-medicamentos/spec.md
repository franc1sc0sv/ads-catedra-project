# Catálogo de Medicamentos

## Overview

Listado maestro de medicamentos: cimiento del módulo Inventario. El encargado (`inventory_manager`) tiene escritura completa; cajero (`salesperson`) y farmacéutico (`pharmacist`) solo lectura.

## Modelo Medicamento

Tabla `medicamentos` con los siguientes campos:

- `id` — bigint, PK
- `cNombre` — string, requerido
- `cDescripcion` — text, nullable
- `cCodigoBarras` — string, **único**, requerido
- `nPrecio` — decimal(10,2), requerido, no negativo
- `nStockActual` — integer, default 0
- `nStockMinimo` — integer, default 0
- `dFechaVencimiento` — date, requerido
- `eCategoria` — enum `CategoriaMedicamento` (`venta_libre`, `requiere_receta`, `controlado`)
- `idProveedor` — FK a `proveedores`
- `bActivo` — boolean, default true (soft-delete lógico)
- `created_at` / `updated_at`

`casts()` mapea `eCategoria` a `CategoriaMedicamento::class` y `dFechaVencimiento` a `date`.

## Enum CategoriaMedicamento

`app/Enums/CategoriaMedicamento.php` — backed enum (string) con `label()`:

- `venta_libre` → "Venta libre"
- `requiere_receta` → "Requiere receta"
- `controlado` → "Controlado"

La categoría dirige el flujo de venta: `controlado` exige captura de receta; `venta_libre` no.

## Listado y filtros

Vista `index` con:

- Búsqueda por `cNombre` o `cCodigoBarras`
- Filtro por `eCategoria`
- Filtro por `idProveedor`
- Filtro por estado (`bActivo` true/false)

## Alta (create)

Form valida `cCodigoBarras` único en toda la tabla (incluso registros inactivos). Si el form incluye `stock_inicial > 0`, el service envuelve en transacción:

1. Inserta el `Medicamento` con `nStockActual = stock_inicial`.
2. Crea movimiento `tipo = ajuste_manual` con `cantidad = stock_inicial` ligado al medicamento.
3. Commit.

Si cualquiera falla, rollback completo.

## Edición

`UpdateMedicamentoRequest` permite editar todos los campos excepto que `cCodigoBarras` mantiene unicidad ignorando el propio registro. El `nStockActual` no se edita aquí — se ajusta vía movimientos.

## Vencido bloquea venta

El service expone `estaVencido(Medicamento $m): bool` que compara `dFechaVencimiento < hoy`. El módulo de ventas llama este check al agregar línea al carrito; si vencido, rechaza la línea con mensaje de error. El `Medicamento` sigue existiendo en catálogo (solo bloqueado para venta).

## Soft-delete vía `bActivo`

Desactivar un medicamento marca `bActivo = false`. Antes de desactivar, el service verifica que no exista venta `EN_PROCESO` que contenga ese medicamento. Si existe, lanza excepción y la operación falla con mensaje "no se puede desactivar: hay ventas en proceso con este medicamento".

Reactivar simplemente vuelve a poner `bActivo = true`.

## Permisos por rol

| Acción | inventory_manager | salesperson | pharmacist |
|---|---|---|---|
| Listar / ver | Sí | Sí | Sí |
| Crear | Sí | No | No |
| Editar | Sí | No | No |
| Activar/desactivar | Sí | No | No |

Aplicado vía middleware `role:` en rutas; nunca chequeado dentro del controller.
