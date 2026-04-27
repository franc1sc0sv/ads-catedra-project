# Validar o rechazar

## What does this part of the system do?
Es el momento de la decisión. El farmacéutico revisa una receta en detalle, la contrasta con la venta que la disparó y resuelve si los medicamentos solicitados son consistentes con la prescripción. Su decisión desbloquea o deja varada la venta.

Cada decisión queda registrada de forma inmutable: quién validó, cuándo y, en caso de rechazo, por qué. Esa huella es el corazón del control regulatorio: si llega una inspección, esta es la evidencia que se muestra.

## Who uses it?
El farmacéutico de turno, después de tomar una receta de la cola de pendientes.

## How does it work?
Desde la cola, el farmacéutico abre la receta y ve toda su información junto con el detalle de la venta vinculada — qué medicamentos controlados están involucrados y en qué cantidad. Cuando alguien abre una receta para revisarla, el sistema la bloquea con un lock corto: si otro farmacéutico intenta abrir la misma receta al mismo tiempo, ve un mensaje "esta receta está siendo revisada por X" y queda en cola; el primero que entró gana, los demás solo verán la decisión final una vez registrada. El lock se implementa con dos campos en la tabla `Receta`: `cveRevisorActual` (FK al farmacéutico que adquirió el lock) y `fLockExpira` (timestamp de expiración). Al abrir la receta el sistema escribe ambos campos; si `fLockExpira` ya pasó, el lock se considera vencido y cualquier farmacéutico puede adquirirlo. Al registrar la decisión, ambos campos se ponen a NULL. Tiene dos caminos. Si todo cumple con la regulación y los medicamentos solicitados encajan con la prescripción, valida la receta y la venta queda automáticamente desbloqueada para que el cajero cierre el cobro; en este caso puede agregar una observación opcional como nota interna — por ejemplo "verifiqué con el médico por teléfono" o "paciente conocido, prescripción habitual". Si detecta cualquier irregularidad — receta vencida, medicamento que no coincide con lo prescrito, datos inconsistentes, cantidad fuera de lo razonable — la rechaza y la observación pasa a ser obligatoria, debe escribir el motivo. Una venta con receta rechazada queda bloqueada hasta que el cajero remueva el medicamento controlado del carrito o cancele la venta entera. Al confirmar la decisión, el sistema guarda el código del farmacéutico, la fecha y hora de validación, la observación si la hubo y el cambio de estado de forma inmutable: una vez registrada la decisión no se puede revertir desde esta pantalla — ni el propio farmacéutico puede modificarla. Si hubo un error, el cajero genera una nueva receta y el flujo arranca de cero.

## Skills relevantes

- `/laravel-specialist` — para la transición de estado y la actualización atómica de la receta y la venta vinculada en la misma transacción.
- `/security-review` — para validar que la huella del farmacéutico no sea modificable y que la auditoría capture toda decisión.
