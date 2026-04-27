# Standards Applied — Alertas de Stock

## authentication/role-middleware

Use `role:<value>` middleware. Reads `$request->user()?->role?->value` each request. Returns 403 if mismatched. Roles: `administrator`, `salesperson`, `inventory_manager`, `pharmacist`. Multiple: `role:administrator,pharmacist`.

---

## authentication/session-auth

Single Laravel session-based auth stack.

---

## backend/php-architecture

Route → FormRequest → Controller → ServiceInterface → Service → Model → Response.

File placement:

- Web controller: `app/Http/Controllers/Web/[Domain]/[Name]Controller.php`
- Service: `app/Services/[Domain]/[Name]Service.php`
- Interface: `app/Services/[Domain]/Contracts/[Name]ServiceInterface.php`

PHP 8.x: readonly constructor, `match` over `switch`, `casts()` method.

---

## backend/service-interface

Every service has a `Contracts/` interface. Controller injects the interface. `AppServiceProvider` binds the interface to its concrete implementation.

---

## frontend/role-namespacing

Each role owns a full slice. Views: `resources/views/{role}/{domain}/{view}.blade.php`.
