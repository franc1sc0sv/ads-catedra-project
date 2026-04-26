# Reportes y Auditoría

Esta sección es el centro de control del administrador de FarmaSys. Combina reportes operativos sobre ventas, inventario, vencimientos y movimientos con la bitácora de auditoría de accesos y acciones sensibles, de modo que el dueño de la farmacia pueda entender cómo va el negocio y, al mismo tiempo, detectar irregularidades del personal.

Todos los reportes son de solo lectura, filtrables por rango de fechas y exportables a CSV. Están pensados para responder preguntas concretas en pocos clics, no para sustituir una herramienta de BI: si el admin quiere saber cuánto vendió la semana pasada, qué medicamentos están por vencer o qué hizo cierto cajero el martes en la tarde, lo encuentra acá.

## What's inside this section

Cuatro reportes que cubren las preguntas operativas y de control más frecuentes de una farmacia pequeña:

- **reporte-ventas** — totales, ticket promedio, cancelaciones y top productos en un rango de fechas.
- **reporte-inventario** — estado actual del stock, valor estimado, productos bajo mínimo y próximos a vencer.
- **reporte-movimientos** — historial filtrable de entradas, salidas, ajustes y bajas para investigar discrepancias.
- **bitacora-auditoria** — registro de acciones sensibles (logins, cambios de usuarios, ajustes, cancelaciones) restringido al administrador.

## What data does this section work with?

Lee de Venta y sus líneas, Medicamento, Movimiento de inventario, Auditoría de acceso y Usuario (referenciado en cada reporte). No modifica datos en ningún caso: solo agrega, filtra y exporta lo que ya generaron las demás secciones.

## What does this section depend on?

Depende de todas las secciones operativas (ventas, inventario, recetas, usuarios) porque consume sus registros, pero ninguna depende de esta — sacarla no rompe la operación, solo deja al admin sin visibilidad.

## Skills relevantes

- `/laravel-specialist` — para las queries con agregaciones, joins y rangos de fecha sobre ventas, inventario y auditoría.
- `/frontend-design` — para que los tableros se sientan accionables y no como tablas planas pegadas en una pantalla.
- `/tailwind-css-patterns` — para los layouts con cards de KPIs, badges de alerta y tablas filtrables.
- `/security-review` — porque la bitácora contiene información sensible y debe quedar restringida exclusivamente al administrador.
