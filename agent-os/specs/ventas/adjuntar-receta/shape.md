# Shape — Adjuntar Receta

## Decisiones de diseño

### `VentaReceta` como tabla de enlace, no estado en `Receta`

Se modela la relación venta-medicamento-receta como una tabla pivot `VentaReceta` con su propia identidad. La alternativa rechazada era guardar `cveVenta` directamente en `Receta`, lo cual rompe la reutilización: una receta debe poder cubrir distintas ventas a lo largo del tiempo (por ejemplo, una receta crónica re-vinculada a varias ventas mensuales). Modelar como link table mantiene `Receta` como entidad limpia con su propio ciclo (PENDIENTE → VALIDADA/RECHAZADA) independiente de cualquier venta.

### Unique en `(cveVenta, cveMedicamento)`

Cada controlado del carrito tiene una receta y solo una. El unique impide doble vinculación accidental dentro de la misma venta. No se pone unique sobre `cveReceta` porque la misma receta puede aparecer en varias ventas (re-vinculación legítima entre ventas distintas).

### Re-vinculación: 4 condiciones, no 3

Las cuatro condiciones (VALIDADA, no ligada a otra venta no-CANCELADA, medicamento coincide, paciente coincide) son todas necesarias:

- Sin "VALIDADA": un cajero podría reutilizar recetas pendientes/rechazadas y saltar la validación.
- Sin "no ligada a otra venta no-CANCELADA": una receta podría usarse dos veces para vender el doble de medicamento controlado, lo que es exactamente lo que la regulación quiere impedir. La excepción de CANCELADA libera recetas de ventas que nunca se concretaron.
- Sin coincidencia de medicamento: se cubriría un controlado distinto al recetado.
- Sin coincidencia de paciente: se vendería controlado a quien no fue recetado.

### Re-vinculación crea fila nueva, no muta `Receta`

Al re-vincular, se inserta una nueva fila en `VentaReceta` apuntando al `cveReceta` existente. La receta original no cambia de estado ni de fecha ni de nada. El audit trail por venta se mantiene en `VentaReceta`; el de la receta misma queda intacto. Esto evita el anti-patrón de "marcar receta como usada" que dificultaría la reutilización legítima.

### Compuerta de cobro en el servicio, no en la vista

`isCobrableAhora(Venta): bool` vive en `VentaService`. La vista solo consulta el booleano para habilitar/deshabilitar el botón de cobro. Razones:

- La regla es de negocio (regulatoria), no de presentación.
- Reusable desde controllers, jobs futuros, comandos artisan.
- Testable sin renderizar Blade.
- Evita duplicar lógica si en el futuro se agrega API (aunque hoy el stack es web-only).

### Auto-desbloqueo, sin paso manual

No existe acción "desbloquear venta". Cuando el farmacéutico valida la última receta pendiente, la siguiente lectura de `isCobrableAhora` devuelve `true`. Mantener el booleano derivado en lugar de un flag persistido elimina una clase entera de bugs por desincronización entre estado de receta y flag de venta.

## Lo que queda fuera

- Notificaciones push al cajero cuando el farmacéutico valida — fuera de alcance MVP.
- Edición de receta después de capturar — si está mal, se rechaza y se captura otra.
- Recetas que cubran múltiples medicamentos en una sola fila — el modelo pide receta-por-controlado.
