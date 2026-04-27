# Tasks: login-web

## Implementation Checklist

- [x] Save spec documentation
- [ ] Create `app/Http/Requests/Auth/LoginRequest.php` — validate `email` (required, email) and `password` (required, string)
- [ ] Update `app/Http/Controllers/Web/Auth/AuthController.php` — add `showLogin()` returning `view('auth.login')`, `login(LoginRequest)` calling `AuthServiceInterface`, and `logout(Request)` with full session invalidation
- [ ] Update `app/Services/Auth/AuthService.php` — implement `redirectPathAfterLogin()` using `match` on `auth()->user()->role->value` for all four `UserRole` values
- [ ] Update `app/Services/Auth/Contracts/AuthServiceInterface.php` — declare `redirectPathAfterLogin(): string`
- [ ] Verify `AppServiceProvider` binds `AuthServiceInterface::class` → `AuthService::class`
- [ ] Create `resources/views/auth/login.blade.php` — login form using `layouts/auth.blade.php`, `x-ui.*` components, single `email` error display, CSRF token
- [ ] Update `routes/web.php` — add `guest` middleware group with `GET /login` → `showLogin`, `POST /login` → `login`; add `POST /logout` → `logout` under `auth` middleware
- [ ] Confirm all PHP files declare `declare(strict_types=1)` at top
- [ ] Run `composer test` — all tests green
- [ ] Run `./vendor/bin/pint` — no style violations
