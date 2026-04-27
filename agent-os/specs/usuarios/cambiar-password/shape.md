# Shape — Cambiar Contraseña

## Decisión central

Dos flujos, dos rutas, dos vistas, dos métodos de servicio. No mezclar en un solo endpoint.

## Por qué dos flujos

- El cambio propio requiere `current_password` para evitar que un atacante con sesión robada cambie la contraseña sin conocer la vieja.
- El reset administrativo NO puede pedir la actual — el admin no la conoce. La autorización viene del rol, no del conocimiento de la contraseña vieja.

Forzar un solo endpoint introduce ramas condicionales (¿es admin?, ¿es otro usuario?, ¿current_password sí o no?) que oscurecen la intención. Mejor dos rutas claras.

## Primitivas del framework usadas

### `Auth::logoutOtherDevices($newPassword)`

Laravel ya resuelve el problema de invalidar sesiones tras cambio de contraseña. Recibe la nueva contraseña en texto plano (la usa para re-hashear el token de sesión actual). Toda otra sesión del mismo `user_id` con un hash distinto queda inválida en el siguiente request.

Solo aplica al cambio propio porque requiere que el usuario afectado sea quien está autenticado. En el reset admin, las sesiones del usuario target se invalidan naturalmente al re-autenticar (el password viejo ya no funciona) — no es perfecto pero es el comportamiento estándar.

### Middleware `password.confirm`

Antes de cambios sensibles, Laravel pide reconfirmar la contraseña aunque la sesión ya esté autenticada. Útil para defender contra sesiones secuestradas o equipos compartidos.

Solo aplica al cambio propio. El reset admin ya está protegido por `role:administrator` — pedirle a un admin que confirme su contraseña por cada reset sería ruido.

### Cast `'hashed'` en `User::casts()`

El modelo ya hashea automáticamente al asignar `$user->password = $plain`. Igual usamos `Hash::make` explícito en algunos contextos por claridad o para documentar.

## Audit trail

Solo el reset administrativo se registra en bitácora. Razón: el cambio propio es una acción de cuenta personal rutinaria (volumen alto, valor de auditoría bajo). El reset administrativo es una acción privilegiada (volumen bajo, valor de auditoría alto: "¿quién reseteó la contraseña de Y?").

Acción registrada: `reset_password_admin`. Entidad afectada: el `User` destino. Actor: `auth()->id()` (el admin).

## Lo que NO hacemos

- No enviamos email de notificación tras cambio de contraseña. Fuera de scope del MVP.
- No forzamos política de complejidad más allá de `min:8`. Fuera de scope.
- No exponemos endpoints API — todo es web/sesión.
- No invalidamos sesiones del target en el reset admin (ver explicación arriba).
