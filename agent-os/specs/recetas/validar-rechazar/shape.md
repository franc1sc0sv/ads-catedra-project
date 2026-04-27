# Shape Notes — Validar o Rechazar Receta

## Decisiones Clave

- **Lock al GET, no al POST.** La adquisición del lock pasa al abrir la pantalla de revisión, no al enviar la decisión. Esto evita que dos farmacéuticos lleguen a redactar observaciones en paralelo.
- **Lock con TTL.** `fLockExpira = now() + 5min`. Sin job de limpieza: el lock expirado simplemente se considera "reclamable" por el siguiente farmacéutico que abra la receta. Cero infraestructura de background jobs para esto.
- **Re-claim sin fricción.** Si el lock expiró, el siguiente GET lo toma. Si pertenece al mismo usuario, se refresca. No hay UI manual de "tomar control".
- **Atomicidad fuerte.** La decisión es una sola transacción que cubre: update de receta + clear de lock + chequeo y unlock de la venta cuando todas las recetas asociadas están validadas. Cualquier fallo intermedio hace rollback completo.
- **Inmutabilidad post-commit.** Una vez en `VALIDADA` o `RECHAZADA`, no se permite re-edición ni de la observación ni del estado. Se valida en el service rechazando si `estado != PENDIENTE`.
- **Observación obligatoria solo en rechazo.** Validar puede tener observación vacía (no aporta valor regulatorio). Rechazar requiere justificación obligatoria — `required_if` en el FormRequest.
- **Auto-unlock de venta.** Solo se desbloquea cuando **todas** las recetas asociadas (vía `venta_receta`) están en `VALIDADA`. Una venta con una receta validada y otra pendiente sigue bloqueada.

## Riesgos

- **Race en adquisición de lock.** Mitigado con `SELECT ... FOR UPDATE` sobre la fila de receta dentro de una transacción durante el GET.
- **Reloj del servidor.** `fLockExpira` se compara contra `now()` del servidor de aplicación. No es problema con un único nodo; con múltiples se asume NTP sincronizado.
- **Receta en venta multi-controlado.** Si una venta tiene N recetas, cada una se decide por separado. La venta solo se libera cuando la última pasa a VALIDADA.

## No Decidido (fuera de scope)

- Cómo se resuelve el camino de "rechazada" desde la perspectiva del cajero (lo cubre la feature "quitar controlado de venta").
- Notificaciones / alertas al cajero.
