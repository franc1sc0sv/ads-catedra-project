# Standards Applied — Ajuste de Stock

## authentication/role-middleware

Use `role:<value>` middleware. Reads `$request->user()?->role?->value` each request. Returns 403 if mismatched. Roles: administrator, salesperson, inventory_manager, pharmacist. Multiple: `role:administrator,pharmacist`. Never check role inside controller.

---

## authentication/session-auth

Single Laravel session-based auth stack. `routes/web.php` → middleware `auth`. Login: `Auth::attempt` + `session()->regenerate`. Logout: `Auth::logout` + `session()->invalidate` + `session()->regenerateToken`. `Hash::make` for passwords; cast `'password' => 'hashed'`.

---

## backend/php-architecture

Route → FormRequest → Controller → ServiceInterface → Service → Model → Response. Controllers thin: call service, return response. File placement:

- Web controller: `app/Http/Controllers/Web/[Domain]/[Name]Controller.php`
- Service:        `app/Services/[Domain]/[Name]Service.php`
- Interface:      `app/Services/[Domain]/Contracts/[Name]ServiceInterface.php`
- Form request:   `app/Http/Requests/[Domain]/[Name]Request.php`

PHP 8.x: backed enum with `label()`, readonly constructor, `match` over `switch`, `casts()` method (Laravel 12).

---

## backend/service-interface

Every service has `Contracts/` interface. Controller injects interface, never concrete class. `AppServiceProvider` binds. Business logic in service.

---

## frontend/role-namespacing

Each role owns full slice: routes, controller, views, nav. Views: `resources/views/{role}/{domain}/{view}.blade.php`. `x-ui.*` shared, `x-nav.*` role-specific.
