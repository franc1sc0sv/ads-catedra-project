# Recetas

Esta sección es el espacio de trabajo del farmacéutico. Cada vez que una venta incluye un medicamento controlado, la receta física que entrega el cliente debe pasar por una validación humana antes de que la venta pueda cerrarse. Aquí se registran los datos de esa receta, se mantiene una cola de pendientes y se conserva la huella de cada decisión.

El objetivo es doble: bloquear ventas que no cumplen la regulación sanitaria y dejar evidencia auditable de quién aprobó qué y cuándo. Es el punto de control regulatorio del sistema.

## What's inside this section

Cuatro flujos cubren el ciclo de vida de una receta, desde que la captura el cajero hasta que queda archivada para auditoría.

- **registro-receta** — el cajero captura los datos de la receta física durante la venta.
- **cola-pendientes** — el farmacéutico ve todas las recetas que esperan validación, ordenadas por antigüedad.
- **validar-rechazar** — el farmacéutico aprueba o rechaza con observación obligatoria, dejando huella inmutable.
- **historial-recetas** — tabla completa filtrable para responder a inspecciones o investigar disputas.

## What data does this section work with?

Recetas con número único, datos del paciente y del médico, fechas de emisión y vencimiento, estado (pendiente, validada, rechazada), y la huella del farmacéutico que tomó la decisión. Cada receta está vinculada a la venta que la disparó.

## What does this section depend on?

Depende de Autenticación y Roles, y conecta directamente con Ventas, que es quien dispara la creación de cada receta.

## Skills relevantes

- `/laravel-specialist` — para el modelo Receta, las relaciones con Venta y el flujo de transición de estados.
- `/tailwind-css-patterns` — para la cola visual del farmacéutico y el formulario de validación.
- `/security-review` — porque las recetas contienen datos médicos sensibles y la validación es punto de control regulatorio.
