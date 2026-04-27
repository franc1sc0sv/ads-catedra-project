# Adjuntar Receta

## Resumen

Cuando una venta incluye uno o más medicamentos clasificados como CONTROLADOS, el cobro queda bloqueado hasta que cada uno de esos medicamentos tenga una receta en estado VALIDADA. El cajero (salesperson) captura la receta o re-vincula una del histórico del paciente; el farmacéutico la valida. El bloqueo de cobro se levanta automáticamente cuando todos los controlados de la venta tienen una receta VALIDADA asociada.

## Modelo de vinculación

La relación entre venta, medicamento y receta vive en una tabla pivot `VentaReceta` con tres columnas:

- `cveVenta` — FK a la venta
- `cveMedicamento` — FK al medicamento controlado dentro de esa venta
- `cveReceta` — FK a la receta que cubre ese medicamento

Lleva un índice unique sobre `(cveVenta, cveMedicamento)`: cada controlado de la venta puede tener exactamente una receta asociada. La receta misma (`Receta`) es una entidad independiente, reutilizable; su estado (PENDIENTE / VALIDADA / RECHAZADA) no depende de la venta.

Cada medicamento controlado del carrito necesita su propia fila en `VentaReceta`. Una receta cubre un medicamento; si la venta tiene N controlados, hay N filas.

## Compuerta de cobro

El servicio expone `isCobrableAhora(Venta): bool`. Devuelve `true` solo cuando, para cada item de la venta cuyo medicamento es CONTROLADO, existe una fila en `VentaReceta` cuya `Receta` asociada está en estado VALIDADA. Si falta una receta, o si alguna está en PENDIENTE o RECHAZADA, devuelve `false` y el flujo de cobro queda bloqueado.

La compuerta vive en el servicio, no en la vista. La vista solo consulta el booleano.

## Dos caminos para vincular

### 1. Capturar receta nueva

El cajero captura los datos requeridos (paciente, médico, fecha, etc.) y se crea una `Receta` nueva en estado PENDIENTE, junto con la fila correspondiente en `VentaReceta`. La receta entra a la cola del farmacéutico para validación.

### 2. Re-vincular receta del histórico

El cajero busca recetas previas del paciente y selecciona una. La operación crea una nueva fila en `VentaReceta` apuntando al mismo `cveReceta` existente. La receta original no se duplica ni cambia de estado.

Una receta es reutilizable para esta venta solo si cumple las cuatro condiciones:

1. Está en estado VALIDADA.
2. No está vinculada a otra venta cuyo estado sea distinto de CANCELADA (es decir, no aparece en `VentaReceta` ligada a una venta EN_PROCESO o COBRADA).
3. Su `cveMedicamento` coincide con el medicamento del carrito que se intenta cubrir.
4. El `cNombrePaciente` de la receta coincide con el paciente declarado en la venta.

`RecetaServiceInterface::findReusableForVenta(Venta, Medicamento, paciente)` aplica este filtro y devuelve la colección candidata.

## Estados y flujo

- Receta nueva: nace PENDIENTE, va a la cola del farmacéutico.
- Validación aprobada: la receta pasa a VALIDADA. Si era la última pendiente de la venta, `isCobrableAhora` empieza a devolver `true`.
- Validación rechazada: la receta queda RECHAZADA. El cobro sigue bloqueado. El cajero puede capturar otra receta para ese controlado, quitar el medicamento del carrito, o cancelar la venta.
- Cliente se va sin cobrar: la venta permanece EN_PROCESO con sus recetas en el estado en que estén.

## Auto-desbloqueo

No hay paso manual de "desbloquear". Cuando el farmacéutico valida la última receta pendiente, la siguiente lectura de `isCobrableAhora(venta)` devuelve `true` y la UI de cobro se habilita.

## Roles

- `salesperson` — captura la receta nueva o ejecuta la re-vinculación; nunca valida.
- `pharmacist` — valida o rechaza recetas pendientes; no captura.

## Resultado esperado

El cajero no puede cobrar una venta con controlados sin que la cola del farmacéutico haya validado las recetas correspondientes. Las recetas previas del paciente se reutilizan sin duplicar registros, manteniendo trazabilidad por venta vía `VentaReceta`.
