# References: Catálogo de Clientes

## app/Http/Controllers/Web/Auth/AuthController.php

**Relevance:** Canonical example of the thin controller pattern used in this project. Shows how a controller delegates all logic to a service, validates input via a form request, and returns `View|RedirectResponse`. `ClienteController` mirrors this exact structure.

## app/Services/Auth/Contracts/AuthServiceInterface.php

**Relevance:** Reference implementation of the service-interface pattern. Demonstrates the interface definition style (return types, method signatures) and how the concrete service is bound in `AppServiceProvider`. `ClienteServiceInterface` follows the same structure.

## app/Enums/UserRole.php

**Relevance:** Defines the role values (`administrator`, `salesperson`, `inventory_manager`, `pharmacist`) used in `EnsureRole` middleware and in the `resolveView()` helper inside `ClienteController`. The `match` expression in `resolveView` must cover the roles expected to access this feature (`administrator` and `salesperson`).

## routes/web.php

**Relevance:** Shows where role-gated route groups are registered and how existing resource routes are structured. The new `clientes` resource and `restore` patch route are added to the same file under the appropriate middleware group.

## app/Providers/AppServiceProvider.php

**Relevance:** Where `ClienteServiceInterface::class → ClienteService::class` binding must be registered, consistent with existing service bindings in the file.

## resources/views/components/ui/

**Relevance:** Shared UI primitives (`button`, `card`, `input`) used in the `create` and `edit` views for both roles. Referencing these keeps the catalog views visually consistent with the rest of the application without duplicating markup.
