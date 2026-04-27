# Standards — Catálogo de Proveedores

## authentication/role-middleware

`role:<value>` middleware. Roles: `administrator`, `salesperson`, `inventory_manager`, `pharmacist`.

---

## authentication/session-auth

Laravel session auth.

---

## backend/php-architecture

Route → FormRequest → Controller → ServiceInterface → Service → Model. PHP 8.x: readonly constructor, `match`, `casts()`.

---

## backend/service-interface

Service + `Contracts/` interface. Controller injects.

---

## frontend/role-namespacing

Views: `resources/views/{role}/{domain}/`.
