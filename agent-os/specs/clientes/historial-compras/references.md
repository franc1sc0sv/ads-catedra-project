# References: historial-compras

## app/Http/Controllers/Web/Auth/AuthController.php

**Relevance:** Canonical example of the thin controller pattern used in this project. Shows readonly constructor injection, single-responsibility methods returning `View|RedirectResponse`, and no business logic in the controller. The `historial` method in `ClienteController` must follow the same shape.

## app/Services/Auth/Contracts/AuthServiceInterface.php

**Relevance:** Reference implementation of the service-interface pattern. `ClienteServiceInterface` (and its `getHistorial` method) must follow the same structure: interface in a `Contracts/` subfolder alongside the concrete service, bound in `AppServiceProvider`.

## app/Http/Middleware/EnsureRole.php

**Relevance:** Enforces role-based access. The `role:salesperson,administrator` middleware used on this feature's routes relies on this middleware reading `auth()->user()->role->value`. No role checks should appear inside the controller.

## app/Enums/UserRole.php

**Relevance:** Defines the role enum values (`administrator`, `salesperson`, `inventory_manager`, `pharmacist`) and their `label()` helper. The middleware string `role:salesperson,administrator` maps directly to these backed enum values.

## routes/web.php

**Relevance:** All web routes for this project live here. The new history route must be added inside the existing `['auth', 'role:salesperson,administrator']` middleware group, or a new group with that exact middleware must be created for the clientes domain.

## resources/views/salesperson/dashboard/ and resources/views/admin/dashboard/

**Relevance:** Existing role-namespaced view directories. The new `clientes/historial.blade.php` views follow the same directory convention — role name as top-level folder under `resources/views/`.
