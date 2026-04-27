# Standards — Catálogo de Medicamentos

## authentication/role-middleware

role:<value> middleware. Multiple roles: role:inventory_manager,salesperson,pharmacist. Never check role inside controller.

---

## authentication/session-auth

Laravel session-based auth.

---

## backend/php-architecture

Route → FormRequest → Controller → ServiceInterface → Service → Model. File placement: app/Http/Controllers/Web/[Domain]/, app/Services/[Domain]/, app/Services/[Domain]/Contracts/, app/Http/Requests/[Domain]/. PHP 8.x: readonly constructor, match, casts().

---

## backend/service-interface

Service + Contracts/ interface. Controller injects interface.

---

## frontend/role-namespacing

Views: resources/views/{role}/{domain}/.
