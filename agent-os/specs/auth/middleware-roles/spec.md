# Spec: middleware-roles

## What EnsureRole Does

`EnsureRole` is a Laravel middleware that enforces role-based access on every incoming request. It sits between the HTTP kernel and the controller, rejecting requests from users whose role does not match the list declared on the route. The middleware is thin by design — it reads a single value, compares it, and either passes the request through or aborts with a 403.

## Why Centralized Role Checking Matters

Role checks scattered across controllers lead to inconsistencies: one controller might forget to check, another might use a different comparison, and a future role rename requires touching many files. By moving the check into a single middleware registered as a named alias (`role`), every route in the system enforces access in exactly the same way. Adding a new protected route is a one-liner. Auditing which routes allow which roles is visible directly in `routes/web.php`.

## How It Reads the Role

On every request, the middleware reads the role from the authenticated session user via `$request->user()?->role?->value`. Laravel's session-based auth already resolved the user before this middleware runs (because `auth` always precedes `role` in the middleware stack). The role is not cached between requests — it is read fresh each time. This means a role change applied to a user in the database takes effect on that user's very next request, with no cache flush required.

## 403 Response

If the authenticated user's role value does not appear in the comma-separated list of roles declared on the route, the middleware calls `abort(403)`. Laravel renders this as a standard 403 Forbidden response. The user sees a clear "access denied" message rather than a silent redirect or an empty page.

## Immediate Effect of Role Changes

Because the role is resolved from the live session user on each request, there is no stale state to invalidate. An administrator who changes another user's role from `salesperson` to `inventory_manager` will see that change enforced starting from the user's next HTTP request. No session invalidation, no cache purge, and no application restart is needed.

## Scope

This middleware applies to all role-protected routes across every section of the application. Every route group that restricts access by role must include both `auth` (to require authentication) and `role:<value>` (to require the correct role). The four valid role values are: `administrator`, `salesperson`, `inventory_manager`, `pharmacist`.
