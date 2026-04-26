# Reporte de Inventario

## What does this part of the system do?
Da una foto del estado actual del inventario de la farmacia en un solo lugar. En vez de obligar al encargado a revisar producto por producto, la pantalla presenta los números clave arriba y deja una tabla detallada abajo para investigar casos puntuales.

Los KPIs cubren la cantidad total de productos activos, el valor estimado del inventario calculado como precio por stock disponible, el número de productos bajo el mínimo, los próximos a vencer dentro de una ventana configurable y los ya vencidos pendientes de baja. Esto permite responder en segundos "¿cuánta plata tengo en stock?", "¿cuántos productos necesito reordenar?" y "¿qué tengo que dar de baja?".

## Who uses it?
El administrador y el encargado de inventario, que son los dos roles que toman decisiones de compra y baja.

## How does it work?
Al entrar, la pantalla calcula los KPIs sobre el inventario actual y muestra la tabla con cada medicamento, su stock, su mínimo, su fecha de vencimiento más próxima, su proveedor habitual y el valor unitario. La ventana de "próximos a vencer" se controla con un selector — por defecto 30 días, pero el usuario puede ampliarla a 60 o 90 si quiere mirar más lejos. Los filtros por categoría, proveedor y estado del stock (normal, bajo mínimo, próximo a vencer, vencido) afectan tanto a la tabla como al recuento de los KPIs, así los números siempre coinciden con lo que se está viendo. Productos sin stock siguen apareciendo en cero porque también son útiles para detectar quiebres. La exportación a CSV respeta los filtros activos.

## Skills relevantes

- `/laravel-specialist` — para las queries de inventario con joins a proveedor, cálculos agregados de valor y filtros combinados.
- `/tailwind-css-patterns` — para las cards de KPIs y la tabla con badges de alerta diferenciados por nivel de criticidad.
