# Validar o Rechazar Receta

## Resumen

El farmacéutico revisa una receta médica asociada a un detalle de venta con producto controlado y emite una decisión: validar o rechazar. La decisión es inmutable y desbloquea (o mantiene bloqueada) la venta correspondiente. La operación está protegida por un mecanismo de lock con expiración para evitar revisiones simultáneas.

## Actores

Solo el rol `pharmacist`.

## Flujo Principal

### 1. Apertura de la receta (GET review)

Al abrir la pantalla de revisión de una receta pendiente (`estado = PENDIENTE`):

- Si la receta no tiene lock activo (sin `cveRevisorActual` o `fLockExpira <= now`), el sistema adquiere el lock de forma atómica:
  - `cveRevisorActual = auth()->id()`
  - `fLockExpira = now() + 5 minutos`
- Si existe un lock activo de **otro** farmacéutico (`cveRevisorActual != auth()->id()` y `fLockExpira > now()`), el sistema muestra "Esta receta está siendo revisada por X" y no permite continuar.
- Si el lock activo pertenece al **mismo** farmacéutico, simplemente refresca `fLockExpira`.
- Si el lock está expirado (`fLockExpira <= now()`), se considera reclamable: el nuevo farmacéutico toma el lock.

La adquisición del lock ocurre dentro de una transacción con `SELECT ... FOR UPDATE` sobre la fila de receta para evitar carreras.

### 2. Decisión (POST decidir)

El farmacéutico envía la decisión con:

- `decision`: enum `validada` o `rechazada` (requerido).
- `observacion`: texto libre. Opcional cuando `validada`, **obligatorio** cuando `rechazada`.

Validaciones previas a aplicar la decisión:

- La receta debe estar en `estado = PENDIENTE`. Si no, se rechaza la operación (decisión inmutable).
- El lock debe pertenecer al farmacéutico actual y no estar expirado. Si expiró o lo tiene otro, se devuelve error "el lock expiró, vuelva a abrir la receta".

Aplicación de la decisión (toda en una sola transacción atómica):

1. Actualiza la receta:
   - `estado = VALIDADA` o `RECHAZADA`
   - `cveValidador = auth()->id()`
   - `fValidacion = now()`
   - `cObservacion = <input>` (NULL si vacío y validada)
   - `cveRevisorActual = NULL`
   - `fLockExpira = NULL`
2. Si `decision = validada`: consulta todas las filas en `VentaReceta` de la misma venta. Si **todas** las recetas asociadas a esa venta están en estado `VALIDADA`, marca la venta como desbloqueada (estado de venta vuelve a permitir continuar el flujo de cobro).
3. Si `decision = rechazada`: la venta queda bloqueada. El cajero deberá quitar el producto controlado de la venta para destrabarla; eso queda fuera del scope de esta feature.

### 3. Inmutabilidad

Una vez confirmada la decisión, la receta no puede volver a `PENDIENTE` ni cambiar de estado. `cveValidador`, `fValidacion`, `cObservacion` quedan como registro permanente de la auditoría regulatoria.

## Estados Relevantes

`Receta.estado`: `PENDIENTE` → `VALIDADA` | `RECHAZADA` (terminal).

## Errores Esperados

| Caso | Respuesta |
|---|---|
| Receta ya decidida | redirect con flash error "esta receta ya fue revisada" |
| Lock de otro farmacéutico activo | view bloqueada con "está siendo revisada por X" |
| Lock expirado al intentar decidir | redirect a GET review (re-adquiere lock) |
| `observacion` vacía con `decision=rechazada` | 422 con error de validación en FormRequest |

## Fuera de Scope

- Reasignación manual de revisor.
- Notificaciones al cajero cuando una receta queda rechazada.
- Edición de la observación tras la decisión.
