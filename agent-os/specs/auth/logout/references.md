# References: Logout

## AuthController — logout method

File: `app/Http/Controllers/Web/Auth/AuthController.php`

The `logout()` method must:
1. Call `$this->authService->logout()` (delegates to service).
2. Return `redirect()->route('login')`.

## AuthServiceInterface — logout contract

File: `app/Services/Auth/Contracts/AuthServiceInterface.php`

Add method signature:
```php
public function logout(): void;
```

## AuthService — logout implementation

File: `app/Services/Auth/AuthService.php`

Implementation:
```php
public function logout(): void
{
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
}
```

## Role nav components — logout button

All four files receive the same POST form with `@csrf`:

- `resources/views/components/nav/admin-nav.blade.php`
- `resources/views/components/nav/salesperson-nav.blade.php`
- `resources/views/components/nav/inventory-manager-nav.blade.php`
- `resources/views/components/nav/pharmacist-nav.blade.php`
