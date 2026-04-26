# Reporte de Ventas

## What does this part of the system do?
Ofrece al administrador una vista resumida y detallada de las ventas hechas en un rango de fechas. La idea es responder en pocos segundos preguntas como "¿cuánto vendimos esta semana?", "¿qué tan seguido se cancelan ventas?" o "¿cuáles son los productos que más rotan?", sin tener que abrir cada venta una por una.

La pantalla combina cinco indicadores de cabecera (número de ventas, ingreso total, ticket promedio, número de cancelaciones y monto cancelado) con dos tablas de apoyo: el listado completo de ventas del período y un top de productos más vendidos. Todo es exportable a CSV para que el dueño pueda llevarse los números a su contador o a una hoja de cálculo.

## Who uses it?
Solo el administrador, que es quien necesita la visión global del negocio.

## How does it work?
El admin entra a la pantalla y por defecto ve el rango del mes en curso. Puede ajustar fechas y aplicar filtros opcionales por método de pago, vendedor (el cajero que cerró la venta) o estado de la venta — completadas, canceladas o ambas. Al cambiar cualquier filtro, las cards de KPIs y las dos tablas se recalculan sobre el mismo conjunto. Si el rango no devuelve nada, las cards quedan en cero y las tablas muestran un mensaje vacío en lugar de filas. Las ventas canceladas siempre se cuentan aparte para que no inflen el ingreso total: el monto cancelado va en su propia card. La tabla de top productos se ordena por unidades vendidas y agrupa por medicamento, así un mismo producto vendido en varias ventas aparece una sola vez con el total acumulado. El botón de exportar genera un CSV con las ventas listadas según los filtros activos.

## Skills relevantes

- `/laravel-specialist` — para escribir las agregaciones SQL eficientes apoyadas en índices sobre la fecha de venta y los joins con líneas y medicamento.
- `/frontend-design` — para que el panel de KPIs y el top de productos se sientan accionables y no como una tabla más.
- `/tailwind-css-patterns` — para las cards de cabecera y las tablas filtrables con estados vacíos claros.
