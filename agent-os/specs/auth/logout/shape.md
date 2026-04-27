# Shape: Logout

## Scope

Minimal. This is a single HTTP endpoint plus a button in four nav components. No new models, no migrations, no jobs.

## Decisions

**No confirmation dialog.** The product spec explicitly states "un clic, sesión cerrada". No intermediate step. Removing friction is intentional given the shared-workstation context where speed of handoff matters.

**Single POST, no GET.** Logout must be a POST (or DELETE) to prevent CSRF-based logouts via image tags or redirects. A `<form method="POST">` with `@csrf` satisfies this without any JS.

**CSRF protected.** The `@csrf` Blade directive is mandatory. Laravel's `VerifyCsrfToken` middleware will reject any logout request without a valid token.

**Redirect to named route `login`.** Hard-coding URLs is avoided. The controller returns `redirect()->route('login')`.

**Service layer.** Even though logout is three lines, it lives in `AuthService::logout()` to keep the controller thin and the pattern consistent with the rest of the auth domain.

## Out of scope

- "Remember me" token revocation — not used in this stack.
- API logout / token invalidation — web-only stack, no Sanctum tokens.
- Audit log of logout events — not in MVP.
- Redirect to a "you have been logged out" interstitial — not requested.

## Standards applied

- `authentication/session-auth` — three-step session teardown
- `authentication/role-middleware` — logout route uses `middleware('auth')` only, no role check
- `backend/php-architecture` — Route → Controller → Service → Response
- `backend/service-interface` — controller injects interface
- `frontend/role-namespacing` — button appears in all four role nav components
