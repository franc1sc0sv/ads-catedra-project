# Shape — Historial de Recetas

## Forma del problema

Una superficie de auditoría. No es operativa: no se valida ni se modifica desde aquí. Solo se consulta el archivo histórico de recetas para responder preguntas como "¿qué pasó con la receta N?", "¿qué rechazó el farmacéutico X la semana pasada?", "¿cuántas recetas anulamos este mes?".

## Decisiones clave

- **Read-only estricto.** Sin botones de acción, sin edición. Cualquier cambio de estado vive en otros flujos (validación, anulación). Esto mantiene la vista simple y elimina ambigüedad sobre qué se puede hacer.
- **Todos los estados incluidos.** A diferencia de la bandeja activa, aquí entran pendientes, validadas, rechazadas, surtidas y anuladas. El historial es el universo completo.
- **Búsqueda exacta por número.** Es el caso de uso real: alguien tiene el número en un papel y quiere encontrar la receta. No hace falta búsqueda fuzzy ni parcial; agrega complejidad sin valor.
- **`cObservacion` como evidencia de auditoría.** En el detalle se muestra textual lo que escribió el farmacéutico al validar/rechazar. Es la pieza más importante para inspecciones y disputas.
- **Venta vinculada cuando existe.** Cierra el ciclo de auditoría: receta surtida → venta concreta. Solo enlace, no se incrusta el detalle de venta.
- **Paginación servidor.** El histórico crece sin techo. No se hace paginación de cliente ni se carga todo en memoria.

## Fuera de alcance

- Exportación a CSV/Excel.
- Reportes agregados (esos viven en su propia spec).
- Edición o reimpresión de recetas desde aquí.
- Búsqueda por contenido del medicamento o por dosis.

## Riesgos / dudas

- Cuando el farmacéutico filtra "su trabajo", ¿se asume `farmaceutico_id = auth()->id()` por defecto, o el filtro empieza vacío y él lo aplica? Por ahora: empieza vacío; el admin nunca tendría default y eso unifica la vista.
- El campo "fecha de validación" puede ser null para recetas pendientes o anuladas antes de tomarse. La columna debe tolerar vacío.
