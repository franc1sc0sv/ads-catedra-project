# References: Marcar Cliente Frecuente

## app/Http/Controllers/Web/Auth/AuthController.php

**Relevance:** Canonical example of the thin controller pattern used across this project. `toggleFrecuente` follows the same shape: inject service interface via readonly constructor, call one service method, return response. No business logic in the controller body.

---

## app/Services/Auth/AuthService.php + Contracts/AuthServiceInterface.php

**Relevance:** Reference implementation of the service-interface pattern. `ClienteService` and `ClienteServiceInterface` must mirror this structure — interface in `Contracts/`, concrete class alongside it, binding in `AppServiceProvider`.

---

## routes/web.php

**Relevance:** Where the new PATCH route must be registered, inside the appropriate `middleware(['auth', 'role:salesperson,administrator'])` group. Check existing client route groups before adding to avoid duplication.

---

## app/Enums/UserRole.php

**Relevance:** Defines the `salesperson` and `administrator` role values used in `role:salesperson,administrator` middleware. Confirm exact string values before writing the route middleware.

---

## resources/views/salesperson/clientes/index.blade.php (expected)
## resources/views/admin/clientes/index.blade.php (expected)

**Relevance:** Target views where the Alpine.js toggle button must be added. May be created by the `catalogo-clientes` feature — verify existence before editing.

---

## resources/views/components/ui/ (directory)

**Relevance:** Home for all shared UI components (`button`, `card`, `input`). The new `badge-frecuente.blade.php` component must live here to follow existing namespacing conventions.

---

## agent-os/specs/clientes/catalogo-clientes/spec.md

**Relevance:** The catalog listing feature likely creates `ClienteController` and the index views. `marcar-frecuente` extends those — check catalog tasks for any already-created controller or view stubs before adding new methods.
