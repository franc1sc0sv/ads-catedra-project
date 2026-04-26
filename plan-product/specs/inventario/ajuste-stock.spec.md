# Ajuste de Stock

## What does this part of the system do?
Permite corregir el stock de un medicamento cuando lo que dice el sistema no coincide con la realidad. Pasa todo el tiempo: el conteo físico no cuadra, un producto se cayó y se rompió, una caja se venció en el estante, un cliente devolvió algo que ya se le había facturado.

En lugar de editar el stock "a mano" como si nada, esta pantalla registra el cambio como un evento con motivo, tipo y responsable. Eso convierte cada corrección en una huella auditable en el historial del producto, no en un cambio invisible.

Es la única vía oficial para tocar el stock fuera de las ventas y los pedidos. Si alguien necesita subir o bajar un número, pasa por aquí.

## Who uses it?
Solo el encargado de inventario.

## How does it work?
El encargado entra a la pantalla de ajuste, busca el medicamento por nombre o código, y elige qué tipo de ajuste está haciendo: ajuste manual cuando el conteo físico no cuadra, baja por vencimiento cuando un producto pasó su fecha, o devolución cuando un cliente regresa algo. Luego indica la cantidad —puede ser positiva si está sumando o negativa si está descontando— y escribe un motivo, que es obligatorio: nada se ajusta sin una razón escrita. Al confirmar, el sistema actualiza el stock del medicamento y al mismo tiempo crea un registro de movimiento que captura el stock anterior, el nuevo, el tipo, el motivo, la fecha y el usuario que lo hizo. Ese registro queda inmutable: no se puede editar ni borrar después. Si el encargado se equivocó —puso una cantidad mal, eligió el medicamento equivocado— el camino correcto es hacer otro ajuste en sentido contrario que compense el error, dejando ambos visibles en el historial. Eso es a propósito: cualquier discrepancia futura tiene que poder rastrearse, y permitir borrar movimientos rompería esa garantía.

## Skills relevantes

- `/laravel-specialist` — para la transacción que actualiza el medicamento y crea el movimiento
- `/security-review` — porque el ajuste manual es la operación con mayor potencial de abuso interno
