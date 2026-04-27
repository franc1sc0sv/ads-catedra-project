# Standards — Historial de Movimientos

## authentication/role-middleware

role:<value> middleware reads $request->user()?->role?->value. Roles: administrator, salesperson, inventory_manager, pharmacist.

---

## authentication/session-auth

Laravel session auth.

---

## backend/php-architecture

Route → Controller → ServiceInterface → Service → Model. File placement standards. PHP 8.x conventions.

---

## backend/service-interface

Service + Contracts/ interface. Controller injects interface.

---

## frontend/role-namespacing

Views: resources/views/{role}/{domain}/.
