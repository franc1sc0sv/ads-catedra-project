# Shape: Cola de Pendientes

## Decisión clave: orden por antigüedad

La cola es **oldest-first** (`fEmision ASC`). El criterio es de urgencia clínica: una receta esperando desde hace más tiempo es la siguiente prioridad. No se ofrece reordenamiento manual ni por fecha descendente — cualquier farmacéutico que abra la pantalla ve la misma cabeza de cola.

## Lock optimista, no bloqueante en la cola

El lock (`cveRevisorActual` + `fLockExpira`) se **muestra** en la cola pero no la **filtra**. Razones:

- Visibilidad: todos los farmacéuticos siguen viendo qué hay en cola, incluso lo que está siendo trabajado.
- Recuperación de fallos: si un farmacéutico abre una receta y se desconecta, el lock vence solo (`fLockExpira` pasa) y la receta vuelve a ser tomable sin intervención manual.
- Doble verificación: aunque la cola muestre la receta como tomable, el toma efectivo del lock se hará al entrar a la pantalla de validación (spec aparte), donde se resuelve cualquier carrera.

Resultado: la tarjeta puede estar en uno de tres estados visuales:
1. **Disponible** — `cveRevisorActual IS NULL` o lock expirado. Click habilitado.
2. **En revisión** — `cveRevisorActual IS NOT NULL` y `fLockExpira > NOW()`. Mostramos el nombre del revisor; click deshabilitado.
3. **Lock expirado con revisor previo seteado** — tratado como Disponible. El nombre del revisor anterior no se muestra; el dato es ruido.

## Cola compartida

No hay asignación. Cualquier farmacéutico ve y puede tomar cualquier receta pendiente. La separación entre farmacéuticos se da únicamente por el lock de validación, no por ownership de la cola.

## Filtros

Solo dos: por médico y por paciente. Se eligen porque son los ejes de búsqueda del farmacéutico cuando un paciente o un consultorio llama preguntando por una receta puntual. Filtros por fecha o por estado se descartan: la cola es por definición pendientes, y el orden cronológico ya cubre la dimensión temporal.

## Lo que NO se decide aquí

- Política de duración del lock (`fLockExpira`) — se define en la spec de validación.
- Política de qué hacer cuando el lock vence con cambios sin guardar — spec de validación.
- Refresh automático de la cola — fuera de alcance del MVP; se asume reload manual.
