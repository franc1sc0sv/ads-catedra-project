# Shape — Historial de Movimientos

## Forma del problema

Auditoría. La pregunta que esta pantalla responde es: *"¿por qué el stock de este medicamento es el que es?"* — y la respuesta es la lista append-only de todos los eventos que lo movieron.

## Decisiones de diseño

### Solo lectura
La UI no permite editar ni borrar. Si hubiera un botón de editar, perderíamos la propiedad de auditoría. Cualquier corrección se modela como un nuevo movimiento de tipo "ajuste manual" que compensa al erróneo. Patrón de **compensating adjustment**, no mutación.

### Eager-load para evitar N+1
El listado renderiza enlaces a la venta o pedido origen. Si se cargan perezosamente, cada fila dispara queries extra. El servicio hace `with(['venta', 'pedido', 'usuario'])` antes de paginar.

### Filtros por fecha + tipo
Son los dos ejes naturales de auditoría: "qué pasó esta semana" y "muéstrame solo las bajas por vencimiento del último mes". Multi-select sobre el enum de tipos.

### Orden cronológico inverso
El caso de uso típico es "qué pasó recientemente". `created_at DESC`.

### Paginación servidor
El historial puede crecer indefinidamente. No se carga todo en memoria.

### Sub-ruta del medicamento
La pantalla no tiene sentido sin un medicamento. Se accede desde la ficha del medicamento y la URL refleja la jerarquía: `/inventario/medicamentos/{medicamento}/movimientos`.

### Vista duplicada por rol
`inventory_manager` y `administrator` ven el mismo contenido, pero cada uno desde su namespace de vistas (chrome distinto: nav, layout). El servicio y controlador son únicos; la vista cambia.

## Riesgos / no resueltos

- ¿Se exporta? No en este sprint.
- ¿Cómo se muestran tipos sin documento origen (ajuste manual)? Sin link, solo el texto de la nota.
- ¿Performance con miles de movimientos? Paginación + índice por `(medicamento_id, created_at)` lo cubre por ahora.
