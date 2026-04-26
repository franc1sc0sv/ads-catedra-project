# Historial de recetas

## What does this part of the system do?
Es el archivo completo de todas las recetas que han pasado por el sistema, sin importar su estado. Sirve para responder a inspecciones sanitarias, investigar disputas con clientes o médicos, y para que el farmacéutico revise lo que validó días atrás.

A diferencia de la cola de pendientes, que es operativa y solo muestra lo que requiere acción, el historial es consulta: nada se decide aquí, solo se busca y se lee.

## Who uses it?
El farmacéutico para revisar su trabajo previo y el administrador para auditoría y respuesta a inspecciones.

## How does it work?
Una tabla lista todas las recetas con sus datos clave: número, paciente, médico, fecha de emisión, estado actual y, si aplica, farmacéutico que validó y fecha de validación. Se puede filtrar por estado (pendiente, validada, rechazada), por médico, por paciente, por farmacéutico que tomó la decisión y por rango de fechas. La búsqueda por número de receta es exacta para localizar rápido un caso específico que mencionó un inspector o un cliente. Cada fila se puede abrir para ver el detalle completo, que incluye la venta vinculada y las observaciones del farmacéutico — tanto si validó dejando una nota interna como si rechazó explicando el motivo. Esa observación es clave en auditorías o disputas: es la voz del farmacéutico explicando por qué tomó la decisión que tomó. Los resultados se paginan para mantener la pantalla ágil.

## Skills relevantes

- `/laravel-specialist` — para la query con filtros combinados, búsqueda exacta por número y paginación.
- `/tailwind-css-patterns` — para la tabla con badges de estado y la vista de detalle expandida.
