# Shape: Crear Pedido

## Forma del problema

Master-detail clásico: una cabecera (`Pedido`) con N líneas (`DetallePedido`). El reto no está en el dibujo del formulario, sino en la atomicidad y en las invariantes que deben sobrevivir incluso si el frontend miente.

## Decisiones de forma

### 1. Persistencia atómica

La creación del pedido y todas sus líneas viven dentro de un único `DB::transaction()` en `PedidoService::create`. Si una línea falla (constraint, FK), todo se revierte. El controlador **no** conoce la transacción.

### 2. Unicidad de medicamento por pedido a nivel DB

`unique(['cvePedido', 'cveMedicamento'])` en `detalle_pedidos`. El `FormRequest` también valida `distinct` en el payload, pero la verdad es la base de datos. Si dos requests concurrentes pasan validación con el mismo medicamento, la DB rechaza el segundo y la transacción aborta.

### 3. El estado lo escribe el servidor, no el cliente

El cliente nunca envía `eEstado`. El service fija `EstadoPedido::SOLICITADO` al crear. Cualquier transición posterior queda fuera de esta spec.

### 4. El total lo calcula el servidor

`nTotal = sum(linea.nCantidad * linea.nPrecioUnitario)` se calcula dentro del service. El frontend muestra un total visual con Alpine para feedback, pero ese número nunca se persiste tal cual.

### 5. Máquina de estados — solo bordes relevantes hoy

```
[ * ] --create--> SOLICITADO --enviar--> ENVIADO --recibir--> RECIBIDO
                  SOLICITADO --cancelar--> CANCELADO
```

Esta spec cubre solo `[*] -> SOLICITADO`. La regla "editable solo en SOLICITADO, locked en ENVIADO/RECIBIDO" se documenta aquí pero se aplica en specs de edición/transición posteriores.

### 6. Forma del formulario

- Cabecera arriba: proveedor (combo de activos), fecha esperada, observaciones.
- Tabla de líneas debajo con botón "Agregar línea" — Alpine maneja un arreglo `lineas` y renderiza filas.
- Cada fila: medicamento (combo), cantidad, precio unitario, subtotal (computed), botón eliminar.
- Total estimado en el pie, también computed.
- Submit estándar (no AJAX). Errores de validación se muestran inline por línea con notación `lineas.0.nCantidad`, etc.

### 7. Defensa en profundidad

| Invariante | FormRequest | Service | DB |
|---|---|---|---|
| Al menos 1 línea | `lineas: required, min:1` | sanity check | — |
| Cantidad > 0 | `min:1` | — | — |
| Precio >= 0 | `min:0` | — | — |
| Medicamento único | `distinct` | — | unique constraint |
| Proveedor activo | `exists + scope` | — | FK |
| Estado inicial | — | hardcoded | default |
| Total correcto | — | recalculado | — |

## Riesgos y contención

- **Race condition en duplicados:** mitigada por unique constraint + transacción que aborta.
- **Cliente envía `eEstado` malicioso:** ignorado; el service no lo lee del request.
- **Cliente manipula `nTotal`:** ignorado; el service recalcula.
- **Proveedor inactivo seleccionado:** rechazado por la regla `exists` con scope activo en el FormRequest.
