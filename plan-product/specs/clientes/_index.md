# Clientes

El catálogo de personas que compran en la farmacia. Aquí se mantienen sus datos de contacto, se identifican los clientes frecuentes para darles trato preferencial y se puede consultar todo lo que han comprado a lo largo del tiempo.

La sección está pensada para que el cajero no tenga que abandonar la pantalla de venta cuando necesita buscar o registrar a un cliente. Es un módulo pequeño en superficie pero crítico para que la venta fluya sin fricción.

## What's inside this section

Cuatro piezas que cubren desde el mantenimiento del catálogo hasta el uso del cliente dentro del flujo de venta y la consulta posterior de su historial.

- **catalogo-clientes** — el listado completo con búsqueda, alta, edición y desactivación de clientes.
- **marcar-frecuente** — un toggle rápido para activar o quitar la bandera de cliente frecuente.
- **busqueda-venta** — el autocompletar embebido en la pantalla de venta y el alta rápida en modal.
- **historial-compras** — todas las ventas asociadas al cliente, ordenadas cronológicamente.

## What data does this section work with?

Trabaja con la entidad Cliente (nombre, contacto, identificación única, bandera de frecuente, estado activo) y consulta de forma inversa la entidad Venta para mostrar el historial de compras de cada persona.

## What does this section depend on?

Depende de Autenticación y Roles para controlar quién puede ver y modificar el catálogo, y se conecta con Ventas tanto para la búsqueda durante la venta como para reconstruir el historial.

## Skills relevantes

- `/laravel-specialist` — para el CRUD del cliente y la búsqueda con autocompletar.
- `/tailwind-css-patterns` — para los formularios, la tabla del catálogo y el componente de autocompletar embebido en la venta.
- `/accessibility` — para que la búsqueda y el formulario de alta rápida sean usables con teclado.
