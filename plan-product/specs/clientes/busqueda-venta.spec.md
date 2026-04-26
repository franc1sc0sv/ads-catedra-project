# Búsqueda de Cliente en Venta

## What does this part of the system do?
Es el puente entre el catálogo de clientes y la pantalla de venta. Permite que el cajero, mientras está armando una venta, encuentre a la persona que está atendiendo y la asocie al ticket sin abandonar el flujo.

Si el cliente todavía no existe en el catálogo, la misma pantalla ofrece un alta rápida en modal para crearlo en el momento, con los datos mínimos, y volver a la venta con ese cliente ya seleccionado.

La meta es que asociar un cliente a la venta nunca sea una excusa para perder el carrito o tener que abrir otra pestaña.

## Who uses it?
Cajeros, durante el flujo de nueva venta.

## How does it work?
En la pantalla de nueva venta hay un campo de búsqueda dedicado al cliente. A medida que el cajero escribe, el sistema sugiere clientes activos cuyo nombre o identificación coincida, mostrando los primeros resultados en un dropdown debajo del campo. Al elegir uno con el clic o con la tecla Enter, queda asociado a la venta y el campo muestra el nombre seleccionado junto con el badge de frecuente si aplica. Si la búsqueda no devuelve nada, en el mismo dropdown aparece la opción "Crear cliente" que abre un modal con un formulario reducido — solo nombre, teléfono e identificación — para registrar al cliente al vuelo. Cuando el cajero crea un cliente desde ese modal, el cliente queda guardado en el catálogo (no solo asociado a esa venta puntual): en adelante aparecerá en futuras búsquedas como cualquier otro, y el resto de los datos se completan después desde el catálogo si hace falta. Al guardar, el modal se cierra, el cliente nuevo queda automáticamente seleccionado en la venta y el carrito que el cajero estaba armando sigue intacto. Los clientes desactivados no aparecen en las sugerencias para no confundir, pero si el cajero intenta registrar una identificación que ya existe (incluso si está desactivado) el modal lo avisa para que el admin lo reactive desde el catálogo en vez de duplicarlo.

## Skills relevantes

- `/laravel-specialist` — para el endpoint de búsqueda con LIKE limitado a clientes activos y un tope de resultados.
- `/tailwind-css-patterns` — para el componente de autocompletar con dropdown y el modal de alta rápida.
