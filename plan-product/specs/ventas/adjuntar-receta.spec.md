# Adjuntar receta

## What does this part of the system do?

Cuando el carrito incluye un medicamento de categoría CONTROLADO, la venta queda bloqueada para cobro hasta que se vincule una receta válida. Este paso captura los datos de la receta física que trae el cliente, la manda a la cola del farmacéutico para validación y, una vez aprobada, libera la venta para que el cajero pueda cobrar.

Es el punto donde el cumplimiento regulatorio se cruza con el flujo comercial: sin receta validada no hay cobro, y sin cobro no hay salida del medicamento. Esa rigidez es intencional.

## Who uses it?

El cajero captura la receta; el farmacéutico la valida o rechaza desde su propia bandeja.

## How does it work?

Al agregar un medicamento controlado al carrito, el panel de cobro queda deshabilitado y aparece una alerta indicando que falta receta. El cajero tiene dos caminos: capturar una receta nueva con los datos físicos —número, médico, cédula del médico, paciente, fechas— o seleccionar una receta ya existente del histórico del paciente.

Para que una receta del histórico aparezca como opción seleccionable deben cumplirse tres condiciones: la receta tiene que estar en estado VALIDADA, no puede estar ya vinculada a otra venta —cada receta valida exactamente una venta— y el paciente y el medicamento controlado del carrito tienen que coincidir con los de la receta. Si alguno de esos criterios falla, la receta no se ofrece y el cajero captura una nueva.

Una receta nueva nace en estado PENDIENTE y se envía a la cola del farmacéutico. Mientras tanto la venta sigue en EN_PROCESO, bloqueada. Cuando el farmacéutico valida, la venta se desbloquea automáticamente y el cajero puede pasar a cobro. Si el farmacéutico rechaza la receta, la venta no puede cerrarse: el cajero tiene que remover el medicamento controlado del carrito o cancelar la venta completa. Si el cliente espera mucho y se va, la venta queda EN_PROCESO con su receta pendiente y se puede retomar luego.

## Skills relevantes

- `/laravel-specialist` — para la regla que bloquea el cobro mientras no haya receta validada y para el desbloqueo reactivo
- `/tailwind-css-patterns` — para el formulario de captura de receta y el indicador de estado (pendiente, validada, rechazada) en la venta
