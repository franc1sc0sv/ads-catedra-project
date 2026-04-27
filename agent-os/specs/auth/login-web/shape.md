# Shape Notes: login-web

## Scope

In scope:
- Login form view (email + password)
- POST handler: `Auth::attempt()`, session regeneration, role-based redirect
- Logout handler: full session invalidation
- `LoginRequest` form request
- `redirectPathAfterLogin()` in `AuthService`
- Route definitions under `guest` and `auth` middleware groups

Out of scope:
- Password reset / forgot password
- Remember-me functionality
- Two-factor authentication
- Account registration
- API authentication

## Decisions

**Single error message for all failure modes.** The spec explicitly requires no discrimination between "email not found", "wrong password", and "inactive account". Using Laravel's built-in `auth.failed` translation key satisfies this.

**Session guard only.** The existing `AuthService` has JWT remnants. Those are ignored. The new implementation uses `Auth::attempt()` + session. The interface contract (`redirectPathAfterLogin()`) is reused as-is.

**`LoginRequest` instead of inline validation.** Keeps the controller thin. Validation is a cross-cutting concern; it belongs in the request class per the `php-architecture` standard.

**`match` for role redirect.** Four roles, four paths. A `match` expression on `UserRole->value` is exhaustive and throws `UnhandledMatchError` if a new role is added without updating the service — intentional fail-fast behavior.

**`guest` middleware on login routes.** Prevents authenticated users from hitting the login page; Laravel redirects them to their intended destination automatically.

## Context

- Existing `AuthController` exists but uses JWT. It must be updated, not replaced, to preserve the file path convention.
- `AuthServiceInterface` already declares `redirectPathAfterLogin()`. Confirm return type is `string`.
- Roles are in `App\Enums\UserRole` with values: `administrator`, `salesperson`, `inventory_manager`, `pharmacist`.
- The `auth.blade.php` layout exists; the login view should extend it.
- `x-ui.*` shared components exist for button, card, input — use them.

## Standards Applied

- `authentication/session-auth`
- `authentication/role-middleware`
- `backend/php-architecture`
- `backend/service-interface`
- `frontend/role-namespacing`
