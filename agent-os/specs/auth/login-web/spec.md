# Spec: login-web

## What It Does

Provides the web-based login screen for all pharmacy staff roles. Users submit email and password; on success, a Laravel session is created and the user is redirected to the dashboard matching their role. On failure (wrong credentials, inactive account, or non-existent email), a single generic error message is shown — no hint about which condition failed.

## Why It Exists

This is the system entry point. No other feature is accessible without it. Role-based dashboard redirection happens at login time, so each role lands in their own context immediately. Without session authentication, all downstream role-protected routes are unreachable.

## Implementation Approach

### Request Lifecycle

```
GET /login  → AuthController@showLogin → view('auth.login')
POST /login → LoginRequest (validation) → AuthController@login → AuthServiceInterface → redirect
POST /logout → AuthController@logout → session invalidation → redirect('/')
```

### Authentication Flow

`Auth::attempt(['email' => $email, 'password' => $password])` handles credential verification and password hash comparison automatically. On success, call `$request->session()->regenerate()` to prevent session fixation, then redirect to `$this->authService->redirectPathAfterLogin()`.

`redirectPathAfterLogin()` uses a `match` on `auth()->user()->role->value` to map each `UserRole` enum value to its dashboard route.

### Failure Handling

Any failure — wrong password, unknown email, inactive account — returns `back()->withErrors(['email' => __('auth.failed')])`. One message, no discrimination.

### Session Logout

`Auth::logout()` + `$request->session()->invalidate()` + `$request->session()->regenerateToken()` then redirect to `/login`.

## Key Technical Decisions

- **No JWT.** Session-only auth per project standard. `Auth::attempt()` + session regeneration is the complete flow.
- **Service owns redirect logic.** The controller does not inspect the role; `AuthService::redirectPathAfterLogin()` uses `match` to return the correct path. Adding a role means editing only the service.
- **LoginRequest for validation.** `email` (required, email format) and `password` (required, string) validated before the controller body runs. Controller stays thin.
- **Generic error message.** Security requirement: do not reveal whether the email exists. Single `auth.failed` message for all failure modes.
- **`role:<value>` middleware on dashboards, not here.** The login controller does not check roles. Dashboards are protected by their own middleware groups; login only authenticates and redirects.
