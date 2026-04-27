# Standards — Bitácora de Auditoría

## authentication/role-middleware

`role:<value>` middleware. 403 served before loading data.

---

## authentication/session-auth

Laravel session auth.

---

## backend/php-architecture

Route → Controller → ServiceInterface → Service → Model.

---

## backend/service-interface

Service + `Contracts/` interface; controllers across the app inject `BitacoraServiceInterface` to `log()` at action points.

---

## frontend/role-namespacing

Views: `resources/views/admin/reportes/`.
