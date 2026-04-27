# Spec: Cola de Pendientes

## Overview

Tablero principal del farmacéutico que muestra las recetas en estado `PENDIENTE` ordenadas por fecha de emisión, con la más antigua primero. Cada tarjeta de la cola resume la información clave: paciente, médico, número de receta, medicamentos controlados incluidos, y el tiempo que la receta lleva esperando.

Solo el rol `pharmacist` accede a esta vista.

## Modelo de datos

La cola se construye sobre el modelo `Receta` con los siguientes campos relevantes:

- `eEstado` — enum `EstadoReceta` con valores `PENDIENTE`, `VALIDADA`, `RECHAZADA`. Esta vista filtra exclusivamente por `PENDIENTE`.
- `cveRevisorActual` — FK nullable a `users.cveUsuario`. Identifica al farmacéutico que tiene la receta abierta en revisión. Es `NULL` cuando nadie la está trabajando.
- `fLockExpira` — timestamp nullable. Marca el momento en que el lock de revisión vence. Cuando es `NULL` o ya pasó, la receta vuelve a estar disponible para cualquier farmacéutico.
- `fEmision` — fecha de emisión de la receta, usada como criterio de orden ascendente.

La receta se relaciona con `Venta` (origen del cobro) y con sus líneas de `Medicamento`, donde algunas pueden estar marcadas como controladas.

## Lógica de la cola

1. La consulta selecciona recetas con `eEstado = PENDIENTE`, ordenadas `fEmision ASC`.
2. Por cada receta se carga `with('venta', 'medicamentos')` para evitar N+1 al renderizar las tarjetas; los medicamentos controlados se determinan con un flag a nivel de medicamento.
3. La presentación del lock se calcula en tiempo de render:
   - Si `cveRevisorActual IS NOT NULL` y `fLockExpira > NOW()`, la tarjeta muestra "en revisión por <nombre>" y el clic queda deshabilitado.
   - Si `fLockExpira` ya pasó (o es `NULL`), la receta es seleccionable aunque `cveRevisorActual` siga seteado — el lock se considera expirado y otro farmacéutico puede tomarla.
4. Filtros opcionales por médico (`cveMedico`) y por paciente (`cvePaciente`) se aplican antes del orden.

## Vista y navegación

- Ruta: `GET /recetas/cola` bajo el grupo `auth` + `role:pharmacist`.
- Vista: `resources/views/pharmacist/recetas/cola.blade.php`.
- Cada tarjeta seleccionable enlaza a la pantalla de validación (spec separada).

## Fuera de alcance

- Tomar el lock al hacer clic — pertenece a la spec de validación.
- Mostrar recetas `VALIDADA` o `RECHAZADA` — esta vista es estrictamente la cola de pendientes.
- Notificaciones en tiempo real cuando aparece una receta nueva.
