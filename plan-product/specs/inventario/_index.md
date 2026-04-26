# Inventario

Esta es el área del encargado de inventario. Acá vive el catálogo de medicamentos con su stock, sus fechas de vencimiento y el proveedor de cada uno. Cuando algo no cuadra, el encargado registra un ajuste manual; cuando un producto está por vencer o cae bajo el mínimo, el sistema avisa; y cada movimiento queda guardado para siempre en un historial que se puede consultar después.

Es la fuente de verdad del stock. El resto del sistema —ventas, pedidos, recetas— consulta acá para saber cuánto hay disponible de cada medicamento.

## What's inside this section

La sección agrupa todo lo que necesita el encargado para mantener el inventario sano: el catálogo en sí, los ajustes cuando hay diferencias, las alertas que avisan a tiempo y el historial que cuenta la historia completa de cada producto.

- **catalogo-medicamentos** — el listado maestro de medicamentos con búsqueda, filtros y altas/bajas
- **ajuste-stock** — la pantalla para corregir el stock cuando el conteo físico no cuadra, hay daño, vencimiento o devolución
- **alertas-stock** — el tablero que muestra qué está bajo el mínimo y qué está por vencer
- **historial-movimientos** — la cronología completa de cada cambio de stock de un medicamento

## What data does this section work with?

Trabaja con medicamentos (catálogo y stock actual) y con movimientos de inventario (cada entrada, salida o ajuste, con su responsable y motivo). Lee también el catálogo de proveedores para asociarlos a cada producto, y consulta la tabla de configuración global del sistema para parámetros como la ventana de días para alertas de vencimiento y el umbral de aviso de stock bajo, que el admin ajusta sin tocar código.

## What does this section depend on?

Depende de Autenticación y Roles para saber quién puede entrar y qué puede hacer, y lee del catálogo de Proveedores para asociar cada medicamento a su origen.

## Skills relevantes

- `/laravel-specialist` — para los modelos Eloquent, las relaciones con Movimiento y los eventos que disparan al cambiar stock
- `/laravel-patterns` — para encapsular el cambio de stock en un servicio que asegure consistencia entre el medicamento y el log de movimientos
- `/tailwind-css-patterns` — para las tablas con filtros, los badges de alerta y el dashboard de stock bajo
