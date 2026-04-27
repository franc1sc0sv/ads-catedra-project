# Shape: middleware-roles

## Scope

Single middleware class: `app/Http/Middleware/EnsureRole.php`.
Registration as the `role` alias in `bootstrap/app.php`.
No new routes, no new views, no new services.

## Decisions

**Read role from session each request — no caching.**
Caching the role (e.g. in the request lifecycle or a static property) would delay role-change propagation. Reading directly from `$request->user()->role->value` on every request is cheap and guarantees immediate consistency.

**Abort 403 on failure — no redirect.**
A redirect to a login page would be misleading (the user is already authenticated). A 403 clearly communicates "you are logged in, but not allowed here."

**Comma-separated role list via variadic parameter.**
Laravel passes pipe/comma-separated middleware parameters as variadic arguments. `role:administrator,pharmacist` becomes `handle($request, $next, 'administrator', 'pharmacist')`. This allows a single route group to permit multiple roles without duplicating the group.

**Middleware is final and has no dependencies.**
No constructor injection needed. The middleware reads from the request directly. Keeping it `final` prevents accidental extension.

**Never check role inside controller.**
Controllers must not contain `if ($user->role === ...)` guards. All role enforcement is declared on the route. This is a hard rule enforced by code review, not by a technical constraint.

## Standards Applied

- `authentication/role-middleware` — primary pattern
- `authentication/session-auth` — role source
- `backend/php-architecture` — file placement, PHP 8.x conventions
- `backend/service-interface` — kept thin, no business logic
- `frontend/role-namespacing` — middleware is the enforcement layer for role-namespaced views

## Open Questions

None. Scope is fully bounded by the existing codebase structure and standards.
