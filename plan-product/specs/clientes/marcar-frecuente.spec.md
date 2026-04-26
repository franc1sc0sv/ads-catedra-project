# Marcar Cliente Frecuente

## What does this part of the system do?
Permite marcar o desmarcar a un cliente como frecuente sin tener que entrar a editar su ficha completa. Es un atajo pensado para cuando el cajero o el administrador reconoce a una persona que viene seguido y quiere dejarlo registrado en el momento.

La bandera de frecuente sirve para que en el siguiente contacto el sistema avise visualmente que se trata de alguien recurrente, y el cajero pueda decidir si le aplica algún descuento o trato preferencial.

## Who uses it?
Cajeros y administradores.

## How does it work?
Tanto desde la fila del cliente en la tabla del catálogo como desde el detalle, hay un toggle pequeño que cambia el estado de frecuente con un solo clic. El cambio se guarda al instante, sin pasos intermedios ni confirmación, porque es una acción de bajo riesgo y reversible. La bandera se muestra como un badge visible en la tabla, en la ficha del cliente y, sobre todo, en la pantalla de venta cuando ese cliente queda seleccionado, para que el cajero sepa de un vistazo si está atendiendo a alguien marcado como frecuente. Si el toggle falla por algún error de red, la interfaz vuelve al estado anterior y muestra un aviso, evitando que el badge mienta sobre el estado real.

## Skills relevantes

- `/laravel-specialist` — para el endpoint del toggle que actualiza solo la bandera y devuelve el nuevo estado.
