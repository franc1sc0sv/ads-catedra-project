---
name: Role Middleware (EnsureRole)
description: Route-level role authorization via jwt_payload attribute — no DB query
type: project
---

# Role Middleware (EnsureRole)

Use `role:<value>` middleware to guard routes by role.

```php
Route::middleware(['auth', 'role:administrator'])->group(...);
Route::middleware(['jwt.auth', 'role:pharmacist'])->group(...);
```

**How it works:**
- API: reads role from `jwt_payload` request attribute (set by `JwtAuthMiddleware`) — no DB query
- Web: falls back to `auth()->user()->role->value`

**Trade-off accepted:** role in JWT is trusted until token expiry. A role change won't take effect until the user's token expires.

Role values (from `UserRole` enum):
- `administrator`
- `salesperson`
- `inventory_manager`
- `pharmacist`

- Pass multiple roles to allow any: `role:administrator,pharmacist`
- Never check roles in controllers — always use this middleware.
