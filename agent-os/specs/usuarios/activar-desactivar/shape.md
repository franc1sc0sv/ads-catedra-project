# Shape — Activar y Desactivar Cuenta

## Forma de la solución

Suspensión suave mediante un flag `bActiva` en `users`. No hay borrado físico, no hay tabla de auditoría dedicada para este MVP, no hay broadcast de eventos. Es un toggle binario administrado por una sola operación de servicio.

## Decisiones clave

### Suspensión, no borrado
La cuenta sigue existiendo. Histórico (ventas, recetas, movimientos de inventario) referencia el `user_id` y permanece consultable. Reportes muestran al usuario aunque esté inactivo. Esto evita romper foreign keys y conserva trazabilidad legal.

### Bloqueo en el login
La verificación se hace dentro de `AuthService::login`, después de validar credenciales y antes de iniciar sesión. Mensaje único: "Cuenta suspendida. Contacte al administrador." No se distingue entre inactivo y credenciales inválidas en logs detallados al usuario para reducir ruido de UX.

### Invalidación perezosa de sesiones (trade-off MVP)
Si un usuario está conectado en el momento de ser desactivado, su sesión actual sigue viva. En el siguiente request, el middleware verifica `auth()->user()->bActiva` y si está en `false` ejecuta logout + invalidate + redirect. La latencia es de un solo request.

Alternativa rechazada para el MVP: mantener un registro de sesiones activas por usuario y marcarlas como invalidadas inmediatamente. Costo de implementación alto, valor marginal para el dominio (farmacia con pocos usuarios concurrentes).

### Reactivación de un solo clic
No hay flujo de aprobación, ni período de gracia, ni notificación al usuario. Administrador clic → `bActiva = true` → siguiente login funciona. Sesiones cerradas durante la inactividad no se reabren — el usuario debe loguear normalmente.

### Histórico intacto
Se prohíbe explícitamente cualquier cascada o borrado al desactivar. La operación toca exactamente una columna en `users`.

## Restricciones

- Solo `administrator` ejecuta el toggle.
- Operación idempotente desde el punto de vista del estado final, pero no se debe llamar dos veces seguidas porque cambiaría el flag.
- La ruta es `PATCH` (mutación parcial sobre el recurso usuario).
- Vista sigue el namespace por rol: `resources/views/admin/usuarios/`.

## Riesgos y mitigaciones

- **Riesgo**: administrador se desactiva a sí mismo y queda fuera del sistema. **Mitigación (futura, fuera del MVP)**: validar en el servicio que el usuario objetivo no sea el `auth()->user()` actual. Por ahora se documenta como restricción operativa.
- **Riesgo**: usuario en medio de una venta es desactivado y pierde el carrito. **Mitigación**: aceptado. La latencia perezosa juega a favor — termina su request actual.
