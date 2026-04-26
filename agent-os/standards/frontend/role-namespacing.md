---
name: Role-Based Namespacing
description: Each role owns its full slice (routes, controller, views, nav) — no cross-role sharing except ui/ components
type: project
---

# Role-Based Namespacing

> Skills: `/tailwind-css-patterns` for Tailwind styling, `/frontend-design` for UI components.

Every role owns a full slice across routes, controllers, views, and nav. No cross-role sharing (except generic `ui/` components).

## Checklist for a new role

1. Add value to `UserRole` enum
2. Add redirect case in `AuthService::redirectPathAfterLogin()`
3. Create controller: `app/Http/Controllers/Web/Dashboard/[Role]Controller.php`
4. Create view folder: `resources/views/[role]/dashboard/index.blade.php`
5. Create nav component: `resources/views/components/nav/[role]-nav.blade.php`
6. Add guarded route group in `routes/web.php`:

```php
Route::middleware(['auth', 'role:[role_value]'])->group(function () {
    Route::get('/[role]/dashboard', [[Role]Controller::class, 'index'])
        ->name('[role].dashboard');
});
```

**Common mistake:** adding routes without the `role:` middleware guard.

- `x-ui.*` components are shared across all roles
- `x-nav.*` components are role-specific — never use another role's nav
