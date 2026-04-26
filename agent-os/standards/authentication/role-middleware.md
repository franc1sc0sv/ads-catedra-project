---
name: Role Middleware (EnsureRole)
description: Route-level role authorization that reads the role from the authenticated user
type: project
---

# Role Middleware (EnsureRole)

Use `role:<value>` middleware to guard routes by role.

```php
Route::middleware(['auth', 'role:administrator'])->group(...);
Route::middleware(['auth', 'role:pharmacist'])->group(...);
```

**Cómo funciona:**
- El middleware lee `$request->user()?->role?->value` en cada request.
- Si no hay usuario autenticado o el rol no coincide con los aceptados, devuelve `403 Unauthorized`.
- Cambiar el rol de un usuario toma efecto en su **siguiente request** (no hay token vigente que esperar).

Valores de rol (enum `App\Enums\UserRole`):
- `administrator`
- `salesperson`
- `inventory_manager`
- `pharmacist`

**Reglas:**
- Para permitir varios roles en una ruta: `role:administrator,pharmacist`.
- Nunca chequear el rol dentro de un controller — siempre vía este middleware.
- Si una pantalla necesita lógica condicional por rol (e.g. mostrar un botón solo para admin), usar `@can` / `Gate` o un check explícito en la vista, pero la autorización dura sigue siendo el middleware en la ruta.
