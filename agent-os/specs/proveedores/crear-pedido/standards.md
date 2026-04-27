# Standards: Crear Pedido

## authentication/role-middleware

`role:<value>` middleware reads `$request->user()?->role?->value`. Returns 403 on mismatch.

---

## authentication/session-auth

Laravel session auth.

---

## backend/php-architecture

Route → FormRequest → Controller → ServiceInterface → Service → Model. Standard file placement. PHP 8.x: `readonly`, `match`, `casts()`.

---

## backend/service-interface

Service + `Contracts/` interface; controller injects the interface, never the concrete class.

---

## frontend/role-namespacing

Views: `resources/views/{role}/{domain}/`.
