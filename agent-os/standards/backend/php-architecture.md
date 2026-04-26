---
name: PHP Architecture
description: Layer structure, file placement conventions, and PHP 8.x patterns used in this Laravel project
type: project
---

# PHP Architecture

> Skills: use `/php-pro` for PHP 8.x features, `/laravel-patterns` for Laravel structure, `/laravel-specialist` for Eloquent/auth.

## Request lifecycle

```
Route → FormRequest (validate) → Controller (delegate) →
ServiceInterface → Service (logic) → Model → Resource → Response
```

Controllers are thin: call service, return response. No business logic.

## File placement

| Class | Path |
|---|---|
| API controller | `app/Http/Controllers/Api/[Domain]/[Name]Controller.php` |
| Web controller | `app/Http/Controllers/Web/[Domain]/[Name]Controller.php` |
| Service | `app/Services/[Domain]/[Name]Service.php` |
| Interface | `app/Services/[Domain]/Contracts/[Name]ServiceInterface.php` |
| Form request | `app/Http/Requests/[Domain]/[Name]Request.php` |
| API resource | `app/Http/Resources/[Name]Resource.php` |
| Enum | `app/Enums/[Name].php` |

## PHP 8.x conventions

```php
// Backed enum with label() helper
enum UserRole: string {
    case ADMINISTRATOR = 'administrator';
    public function label(): string { ... }
}

// Readonly constructor promotion
public function __construct(
    private readonly JwtService $jwtService
) {}

// match over switch
return match($user->role) {
    UserRole::ADMINISTRATOR => route('admin.dashboard'),
    ...
};

// Null-safe operator
$role = $payload?->role ?? $request->user()?->role?->value;

// casts() method (not $casts property) — Laravel 12
protected function casts(): array {
    return ['role' => UserRole::class, 'password' => 'hashed'];
}
```
