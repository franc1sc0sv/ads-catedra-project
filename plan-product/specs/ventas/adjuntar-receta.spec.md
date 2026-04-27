# Adjuntar receta

## What does this part of the system do?

Cuando el carrito incluye uno o más medicamentos de categoría CONTROLADO, la venta queda bloqueada para cobro hasta que cada medicamento controlado tenga una receta válida vinculada. Este paso captura los datos de la receta física que trae el cliente, la manda a la cola del farmacéutico para validación y, una vez que todas las recetas pendientes son aprobadas, libera la venta para que el cajero pueda cobrar.

Es el punto donde el cumplimiento regulatorio se cruza con el flujo comercial: sin receta validada para cada controlado no hay cobro, y sin cobro no hay salida del medicamento. Esa rigidez es intencional.

## Who uses it?

El cajero captura la receta; el farmacéutico la valida o rechaza desde su propia bandeja.

## How does it work?

Al agregar un medicamento controlado al carrito, el panel de cobro queda deshabilitado y aparece una alerta por cada controlado sin receta cubierta. Cada controlado necesita su propia receta: si el carrito tiene dos medicamentos controlados distintos, el cajero debe adjuntar dos recetas, una por cada uno. El bloqueo se levanta solo cuando todos los controlados del carrito tienen una receta en estado VALIDADA vinculada.

La vinculación se gestiona a través de la tabla `VentaReceta`, que relaciona una venta, un medicamento controlado y la receta que lo autoriza en esa transacción. La restricción única `(cveVenta, cveMedicamento)` en `VentaReceta` impide que un mismo controlado tenga más de una receta activa en la misma venta.

Para cada controlado sin cubrir, el cajero tiene dos caminos: capturar una receta nueva con los datos físicos —número, médico, cédula del médico, paciente, fechas— o seleccionar una receta ya existente del histórico del paciente re-vinculándola a la venta actual.

Para que una receta del histórico aparezca como opción seleccionable deben cumplirse cuatro condiciones: la receta tiene que estar en estado VALIDADA; no puede estar ya vinculada a una venta COMPLETADA o EN_PROCESO activa —verificado consultando `VentaReceta` JOIN `RegistroVentas` filtrando por `eEstado != CANCELADA`—; el campo `cveMedicamento` de la receta debe coincidir con el medicamento controlado del carrito; y el nombre del paciente (`cNombrePaciente`) debe coincidir. Si alguno de esos criterios falla, la receta no se ofrece y el cajero captura una nueva.

Cuando el cajero elige re-vincular una receta del histórico, el sistema crea una nueva fila en `VentaReceta` apuntando al mismo `cveReceta`. La receta original no cambia de estado. La restricción de negocio es: una receta no puede aparecer en `VentaReceta` de más de una venta no-CANCELADA simultáneamente.

Una receta nueva nace en estado PENDIENTE y se envía a la cola del farmacéutico. Mientras la venta tenga algún controlado con receta aún en PENDIENTE, sigue bloqueada. Cuando el farmacéutico valida la última receta pendiente, la venta se desbloquea automáticamente. Si el farmacéutico rechaza una receta, el cobro no puede avanzar mientras ese controlado siga en el carrito: el cajero tiene que remover ese medicamento del carrito o cancelar la venta completa. Si el cliente se va antes de que el farmacéutico decida, la venta queda EN_PROCESO con sus recetas pendientes y se puede retomar luego.

## Skills relevantes

- `/laravel-specialist` — para la lógica de bloqueo por controlados y el desbloqueo reactivo al validar la última receta
- `/tailwind-css-patterns` — para el formulario de captura y los indicadores de estado por cada receta en la venta
