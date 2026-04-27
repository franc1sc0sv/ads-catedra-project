# Shaping Notes — Listado de Usuarios

## Decisions

### Combinable filters
Search (nombre/correo) + filtro de rol + filtro de estado se aplican juntos como AND. La query string lleva todos los parámetros y la paginación los preserva con `->withQueryString()`. Esto evita estado en sesión y hace la URL compartible.

### `fUltimoAcceso` se actualiza en el login flow, no por cron
La columna se persiste directamente en `AuthController::login` tras `Auth::attempt()` exitoso. Razones:
- MVP: una sola escritura, en el lugar obvio
- No requiere event listener separado ni `Auth::login` event subscription
- La info quedará desfasada solo si el usuario no vuelve a loguearse, lo cual es exactamente la semántica que queremos

Listener separado se puede introducir después si aparecen otros puntos de entrada (API tokens, SSO) que también deban actualizar la columna.

### Acciones por fila como enlaces directos, sin modal
- Editar → `GET /admin/usuarios/{user}/edit`
- Cambiar password → `GET /admin/usuarios/{user}/password`
- Toggle estado → `POST /admin/usuarios/{user}/toggle` con CSRF y redirect back

Sin Alpine modal en MVP. Reduce JS y mantiene el panel funcional sin interacciones SPA.

### Paginación a 15 items
Estándar de Laravel; la lista de personal de una farmacia es pequeña (decenas, no miles). Ajustable luego sin migración.

### Estado vacío con dos mensajes
- Si hay filtros aplicados y resultado vacío → "No hay usuarios que coincidan con los filtros. [Limpiar filtros]"
- Si no hay usuarios en absoluto → "Aún no hay usuarios registrados."

## Excluido del MVP

- Exportación CSV (lo cubre la bitácora general)
- Auditoría/log de cambios dentro del panel (lo cubre la bitácora general)
- Modal de confirmación para toggle estado (se hará vía form submit + redirect back con flash)
- Bulk actions
- Ordenamiento por columna (orden default: nombre asc)

## Riesgos y Trade-offs

- **Performance:** filtros combinados sobre tabla `users` con ILIKE son aceptables hasta miles de filas. Si crece se añade índice trigram en PG.
- **`fUltimoAcceso` no captura logout/session expiry:** acepta. Tracking de logout queda fuera de alcance.
- **Toggle estado sin confirmación:** acepta. Se mitiga con flash message + acción reversible.
