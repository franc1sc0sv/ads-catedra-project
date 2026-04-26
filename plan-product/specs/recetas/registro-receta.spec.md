# Registro de receta

## What does this part of the system do?
Cuando una venta incluye un medicamento controlado, el cajero debe capturar los datos de la receta física que el cliente entrega en mostrador. Este flujo registra esos datos y los vincula a la venta en curso, dejándola bloqueada hasta que un farmacéutico la valide.

Es la puerta de entrada al circuito de validación. Sin receta capturada, una venta con controlados no puede avanzar hacia el cobro.

## Who uses it?
El cajero, en medio del armado de una venta que incluye al menos un medicamento controlado.

## How does it work?
Al detectar que la venta tiene un controlado, el sistema abre un formulario de captura embebido en el flujo de venta. El cajero ingresa el número de receta, el nombre del paciente, el nombre y la cédula del médico que prescribió, la fecha de emisión y la fecha de vencimiento. El número de receta es único a nivel global: si otro cliente — incluso con otro médico — ya entregó previamente una receta con el mismo número, el sistema bloquea el registro y el cajero verifica la discrepancia con el cliente, ya que normalmente significa que el médico talonó el mismo bloque o que se está intentando reusar una receta. También antes de guardar, el sistema verifica que la fecha de vencimiento no haya pasado al día de la venta; si la receta ya está vencida, el registro se bloquea y se le pide al cliente una receta vigente. Al guardar correctamente, la receta queda en estado pendiente y la venta queda asociada a ella, bloqueada a la espera del farmacéutico. Cada receta valida una sola venta: no se puede reusar después.

## Skills relevantes

- `/laravel-specialist` — para el form request con validación de fechas, unicidad de número y vinculación a la venta.
- `/tailwind-css-patterns` — para el formulario de captura embebido en el flujo de venta.
