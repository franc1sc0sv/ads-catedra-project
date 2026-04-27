# Tasks: Logout

- [x] Spec and task list written (this file)
- [x] Add `POST /logout` route in `routes/web.php` with `middleware('auth')` and name `logout`
- [x] Add `logout()` method to `AuthServiceInterface` in `app/Services/Auth/Contracts/AuthServiceInterface.php`
- [x] Implement `logout()` in `AuthService`: `Auth::logout()` + `session()->invalidate()` + `session()->regenerateToken()`
- [x] Add `logout()` method to `AuthController` — call service, redirect to login
- [x] Add logout button (POST form with CSRF) to `resources/views/components/nav/admin-nav.blade.php`
- [x] Add logout button (POST form with CSRF) to `resources/views/components/nav/salesperson-nav.blade.php`
- [x] Add logout button (POST form with CSRF) to `resources/views/components/nav/inventory-manager-nav.blade.php`
- [x] Add logout button (POST form with CSRF) to `resources/views/components/nav/pharmacist-nav.blade.php`
