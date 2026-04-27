# Shaping Notes — Catálogo de Medicamentos

## Decisiones clave

- **Código de barras único.** Constraint a nivel DB (`unique()` en migración) y en `FormRequest` con `Rule::unique('medicamentos', 'cCodigoBarras')->ignore($this->medicamento)` en update. Dos medicamentos no pueden compartir barcode aunque uno esté inactivo — facilita reactivación sin colisión.

- **Enum dirige el flujo de venta.** `eCategoria` no es solo metadato:
  - `venta_libre` → línea simple, sin requisitos extra.
  - `requiere_receta` → recomienda receta pero no obliga (criterio del farmacéutico).
  - `controlado` → exige campos de receta capturados al cerrar venta.
  El módulo de ventas lee la categoría para decidir el flujo. Mantener el enum pequeño y cerrado.

- **Vencido bloquea al agregar al carrito**, no al cierre. Validación temprana evita armar un carrito imposible. El medicamento permanece visible y editable en catálogo — el bloqueo es del lado de ventas.

- **`stock_inicial` como atajo en alta.** En vez de obligar al encargado a hacer dos pasos (crear medicamento, luego registrar movimiento de ajuste), si el form trae `stock_inicial > 0` el service crea ambos en una transacción. Esto mantiene la traza de auditoría: todo `nStockActual` tiene un movimiento que lo justifica.

- **Soft-delete vía flag `bActivo`, no `SoftDeletes` trait.** Razón: el dominio ya tiene un concepto explícito de "inactivo" que aparece en filtros del UI y en reglas (vencido + inactivo = no aparece en búsqueda de venta). Usar flag manual evita el ruido de `deleted_at`.

- **Guard contra desactivación con venta EN_PROCESO.** Si un cajero tiene una venta abierta con el medicamento en línea y el encargado intenta desactivarlo, la operación falla. Mensaje claro: "quita la línea de la venta EN_PROCESO antes de desactivar". Implementado en `desactivar()` del service, dentro de transacción con lock para evitar race.

## Cosas que NO entran

- Historial de precios — fuera de scope; precio actual sobreescribe.
- Múltiples proveedores por medicamento — un solo `idProveedor` por ahora.
- Lotes / fechas de vencimiento múltiples por medicamento — un solo `dFechaVencimiento`. Si se requiere lotes, es feature aparte.

## Riesgos

- Tres rutas-vistas paralelas (admin/sales/pharma) puede ser duplicación. Mitigar compartiendo partials en `resources/views/components/inventario/`.
- La lógica de "vencido" vive en el service de medicamento pero la consume el módulo de ventas — dependencia explícita vía la interfaz.
