# Registro de Receta

## Resumen

Cuando una venta incluye al menos un medicamento controlado, el cajero (salesperson) debe capturar los datos de la receta física que ampara cada medicamento controlado del carrito. Sin una receta válida y vigente registrada para cada controlado, la venta no puede avanzar al cobro y queda bloqueada.

## Actor y contexto

- **Actor:** salesperson (cajero), durante el armado de una venta.
- **Disparador:** el sistema detecta en el carrito uno o más medicamentos marcados como controlados que aún no tienen receta vinculada.
- **Resultado esperado:** cada medicamento controlado del carrito queda vinculado a una `Receta` en estado `PENDIENTE` mediante un registro en `VentaReceta`. La venta permanece bloqueada hasta que todos los controlados tengan receta y, posteriormente, hasta que cada receta sea validada (flujo aparte).

## Modelo de datos

### Tabla `recetas`

Campos relevantes para esta feature:

- `cveReceta` — PK.
- `nNumeroReceta` — número de receta físico. **Único globalmente** en toda la tabla. Este es el número que aparece en el papel emitido por el médico.
- `cNombrePaciente` — texto.
- `cNombreMedico` — texto.
- `cCedulaMedico` — texto.
- `fEmision` — fecha de emisión que aparece en la receta.
- `fVencimiento` — fecha de vencimiento que aparece en la receta.
- `eEstado` — enum; al guardar siempre se inserta como `PENDIENTE`. La transición a `VALIDADA` / `RECHAZADA` corresponde a otra feature.
- `cveMedicamento` — FK al medicamento controlado al que ampara la receta. **El sistema lo toma del carrito**, no es un input del cajero.
- timestamps.

### Tabla pivote `venta_receta`

- `cveVenta` — FK a `ventas`.
- `cveMedicamento` — FK a `medicamentos`.
- `cveReceta` — FK a `recetas`.
- **Unique compuesto:** `(cveVenta, cveMedicamento)` — un mismo medicamento dentro de una venta no puede vincularse a más de una receta.
- timestamps.

## Flujo

1. El cajero está armando la venta. El sistema detecta que existe al menos un medicamento controlado en el carrito sin receta asociada.
2. El sistema bloquea el avance al cobro y abre el formulario embebido de receta para el primer medicamento controlado pendiente. El formulario muestra (no editable) el medicamento al que aplica.
3. El cajero captura: `nNumeroReceta`, `cNombrePaciente`, `cNombreMedico`, `cCedulaMedico`, `fEmision`, `fVencimiento`.
4. Al enviar el formulario, el servicio valida:
   - `nNumeroReceta` no existe en la tabla `recetas` (único global).
   - `fVencimiento >= fecha del día de la venta`. Si está vencida, **bloquea** y devuelve error.
   - El `cveMedicamento` proviene del contexto de carrito asociado a la venta en curso, no del request del usuario.
5. Si la validación pasa, el servicio ejecuta una **transacción atómica**:
   - `INSERT` en `recetas` con `eEstado = PENDIENTE` y `cveMedicamento` tomado del carrito.
   - `INSERT` en `venta_receta` con `(cveVenta, cveMedicamento, cveReceta)`.
6. Si el carrito tiene más medicamentos controlados pendientes, el sistema repite el formulario con el siguiente. Cuando ya no quedan controlados sin receta, la venta sigue bloqueada al cobro pero por la siguiente puerta (validación de receta), no por captura.

## Reglas de negocio

- Sin receta capturada por cada controlado, la venta no avanza a cobro.
- `nNumeroReceta` es identificador único global del documento físico — dos ventas distintas no pueden registrar el mismo número.
- Una receta vencida al día de la venta **no se acepta**.
- El `cveMedicamento` siempre se obtiene del estado del carrito en el servidor (anti-tamper). El formulario nunca confía en un `cveMedicamento` enviado por el cliente.
- La inserción de `Receta` y `VentaReceta` ocurre en una sola transacción; si falla cualquiera de las dos, no queda residuo.

## Fuera de alcance

- Validación de la receta (paso `PENDIENTE` → `VALIDADA`) — feature separada.
- Edición / anulación de receta ya capturada.
- Subida de imagen escaneada de la receta.
