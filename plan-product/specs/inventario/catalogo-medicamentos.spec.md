# Catálogo de Medicamentos

## What does this part of the system do?
Mantiene el listado maestro de todos los medicamentos que la farmacia vende. Cada medicamento tiene su nombre, descripción, código de barras, precio, stock actual, stock mínimo, fecha de vencimiento, categoría y proveedor asociado.

Es la pantalla donde el encargado da de alta productos nuevos, edita los datos cuando algo cambia (típicamente el precio o el proveedor) y da de baja los que dejan de venderse. También es el lugar al que el cajero y el farmacéutico llegan a buscar información cuando necesitan consultar un producto antes de venderlo o despacharlo.

El catálogo es el cimiento del resto del módulo: sin un medicamento bien dado de alta, no se le puede ajustar stock, no aparece en alertas y no se puede vender ni pedir.

## Who uses it?
El encargado de inventario lo administra con permisos de escritura; el cajero y el farmacéutico lo consultan en modo lectura.

## How does it work?
El encargado abre la lista y ve todos los medicamentos con su stock actual y un indicador visual de cuáles están bajo el mínimo. Tiene una barra de búsqueda que recorre el nombre, el código de barras y opcionalmente la descripción —útil cuando llega un producto físico y solo se tiene la caja en la mano, o cuando el cajero recuerda parte del nombre comercial pero no exacto— y filtros para acotar por categoría (venta libre, requiere receta, controlado), por proveedor o por estado activo. Cuando crea uno nuevo, captura todos los datos en un formulario; el código de barras tiene que ser único en todo el sistema, así que si ya existe el sistema lo avisa antes de guardar. Si captura un stock inicial mayor a cero, ese arranque no aparece de la nada: se registra automáticamente como un movimiento de tipo ajuste manual, así el historial del producto queda limpio y consistente desde el día uno. Para editar entra al detalle, cambia los datos y guarda; para dar de baja no se borra el registro, solo se marca como inactivo, así los reportes históricos siguen funcionando aunque el producto ya no esté en venta.

Un medicamento con la fecha de vencimiento ya pasada no puede venderse: el sistema bloquea el intento de agregarlo al carrito y, además, lo muestra en el tablero de alertas marcado como "vencido — pendiente de baja". El encargado lo retira del catálogo registrando un movimiento de baja por vencimiento, que descuenta el stock restante y deja la huella en el historial.

Si el encargado desactiva un medicamento que en ese momento aparece en líneas de ventas en proceso —carritos abiertos por el cajero pero todavía no cobrados—, el sistema bloquea el cierre de esas ventas hasta que el cajero quite la línea afectada. Las ventas ya completadas no se ven afectadas: siguen referenciando al medicamento aunque esté inactivo, porque el registro histórico no se toca.

## Skills relevantes

- `/laravel-specialist` — para el CRUD con form requests y el unique de código de barras
- `/tailwind-css-patterns` — para la tabla con filtros, búsqueda y badges de categoría
