# Cola de pendientes

## What does this part of the system do?
Es el tablero principal del farmacéutico. Reúne todas las recetas que esperan decisión y las presenta ordenadas para que la atención fluya sin dejar clientes esperando más de lo necesario.

Funciona como bandeja compartida: cualquier farmacéutico de turno puede tomar cualquier receta. La idea es minimizar el tiempo entre que el cajero captura la receta y el farmacéutico se sienta a revisarla.

## Who uses it?
El farmacéutico, como pantalla de inicio cuando entra a su turno.

## How does it work?
Al iniciar sesión, el farmacéutico ve un tablero con todas las recetas en estado pendiente, ordenadas por fecha de emisión de la más antigua a la más reciente, para que las que llevan más tiempo esperando salten a la vista primero. Cada tarjeta resume el paciente, el médico, el número de receta, los medicamentos controlados involucrados en la venta y un indicador visual del tiempo que lleva en cola. Si una receta ya fue tomada por otro farmacéutico que la está revisando en ese momento, aparece marcada con un indicador "en revisión por X" y no se puede abrir hasta que el lock expire por inactividad o el otro farmacéutico termine y registre su decisión; esto evita que dos personas validen la misma receta a la vez. El indicador se lee de los campos `cveRevisorActual` y `fLockExpira` de la tabla `Receta`; si `fLockExpira` ya pasó, el sistema considera la receta disponible aunque `cveRevisorActual` no sea NULL. Cuando hay muchas recetas, el farmacéutico puede filtrar por médico o por paciente para localizar una específica. Al hacer clic sobre una tarjeta libre, accede a la pantalla de validación con todo el detalle.

## Skills relevantes

- `/laravel-specialist` — para la query con filtro por estado, ordenamiento por antigüedad y eager-loading de la venta vinculada.
- `/tailwind-css-patterns` — para el tablero tipo lista con tarjetas y los indicadores de tiempo de espera.
