# Spec: Logout

## What it does

Logout destroys the current user's session and redirects them to the login screen. Once completed, the browser cookie is no longer valid and any subsequent request to a protected route will be redirected to login.

## Why session destruction matters in a shared-computer pharmacy

Pharmacy workstations are shared across shifts. Without an explicit logout, the next employee could inadvertently continue the previous session — accessing another user's transaction history, inventory records, or performing actions under the wrong identity. A single-click logout that fully invalidates the session is a basic safety requirement in this environment.

## How it works

The user clicks "Cerrar sesión" from any screen. This triggers a POST request to `/logout`. The server:

1. Calls `Auth::logout()` to forget the authenticated user.
2. Calls `session()->invalidate()` to destroy the session data and generate a new session ID.
3. Calls `session()->regenerateToken()` to rotate the CSRF token so any captured form tokens become invalid.
4. Redirects the user to the login route.

No confirmation dialog is shown. One click, session gone, back to login.

## Implementation approach

- Route: `POST /logout` protected by the `auth` middleware, named `logout`.
- Controller method: `AuthController@logout` in `app/Http/Controllers/Web/Auth/AuthController.php`.
- The controller delegates to `AuthServiceInterface::logout()` following the service-interface pattern. Business logic (the three-step session teardown) lives in `AuthService`, not the controller.
- The logout button in each role's nav component submits a `<form method="POST" action="{{ route('logout') }}">` with a `@csrf` directive.
- All four role nav components receive the button: `admin-nav`, `salesperson-nav`, `inventory-manager-nav`, `pharmacist-nav`.
