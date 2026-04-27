# Registro de receta

## What does this part of the system do?
Cuando una venta incluye un medicamento controlado, el cajero debe capturar los datos de la receta física que el cliente entrega en mostrador. Este flujo registra esos datos y los vincula al medicamento controlado específico dentro de la venta en curso, dejándola bloqueada hasta que un farmacéutico la valide.

Si la venta tiene varios medicamentos controlados, este formulario se presenta una vez por cada uno que no tenga receta cubierta.

Es la puerta de entrada al circuito de validación. Sin receta capturada para cada controlado, la venta no puede avanzar hacia el cobro.

## Who uses it?
El cajero, en medio del armado de una venta que incluye al menos un medicamento controlado.

## How does it work?
Al detectar que un medicamento controlado no tiene receta vinculada, el sistema abre un formulario de captura embebido en el flujo de venta. El cajero ingresa el número de receta, el nombre del paciente, el nombre y la cédula del médico que prescribió, la fecha de emisión y la fecha de vencimiento. El medicamento controlado al que aplica la receta (`cveMedicamento`) no lo ingresa el cajero: el sistema lo toma del ítem del carrito que disparó el bloqueo y lo guarda automáticamente al crear el registro en `Receta` y al insertar la fila correspondiente en `VentaReceta`.

El número de receta es único a nivel global: si otro cliente —incluso con otro médico— ya entregó previamente una receta con el mismo número, el sistema bloquea el registro y el cajero verifica la discrepancia con el cliente, ya que normalmente significa que el médico talonó el mismo bloque o que se está intentando reusar una receta. También antes de guardar, el sistema verifica que la fecha de vencimiento no haya pasado al día de la venta; si la receta ya está vencida, el registro se bloquea y se le pide al cliente una receta vigente.

Al guardar correctamente, la receta queda en estado PENDIENTE, se crea la fila en `VentaReceta` ligando esta receta al medicamento controlado dentro de la venta, y la venta permanece bloqueada a la espera del farmacéutico. Si quedan otros controlados sin receta en el carrito, el formulario se repite para el siguiente.

## Skills relevantes

- `/laravel-specialist` — para el form request con validación de fechas, unicidad de número y la inserción atómica en `Receta` + `VentaReceta`.
- `/tailwind-css-patterns` — para el formulario de captura embebido en el flujo de venta.
