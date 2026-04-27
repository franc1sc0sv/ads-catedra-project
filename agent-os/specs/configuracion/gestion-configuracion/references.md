# References: Gestión de Configuración Global

## app/Http/Controllers/Web/Auth/AuthController.php

**Relevance:** Canonical example of the thin-controller pattern used in this project. The `ConfiguracionController` must follow the same structure: validate input via a Form Request, delegate all logic to the injected service, and return `View|RedirectResponse`. No business logic in the controller body.

---

## app/Services/Auth/Contracts/AuthServiceInterface.php

**Relevance:** Canonical example of the service-interface pattern. The `ConfiguracionServiceInterface` must follow the same structure: typed method signatures, `declare(strict_types=1)`, namespace under `App\Services\{Domain}\Contracts`. The concrete service lives one level up at `App\Services\{Domain}\{Name}Service.php`.

---

## app/Enums/UserRole.php

**Relevance:** Example of a backed enum with a `label()` helper method. If `eTipoDato` is extracted to a PHP enum (e.g. `App\Enums\TipoDato`), it must follow the same pattern: backed string enum, `label()` method, `declare(strict_types=1)`.

---

## app/Providers/AppServiceProvider.php

**Relevance:** Location where the `ConfiguracionServiceInterface → ConfiguracionService` binding must be registered. Follow the existing binding style already present in this file.

---

## routes/web.php

**Relevance:** All routes must be added inside the existing `['auth', 'role:administrator']` middleware group. Do not create a new group; append to the existing administrator group.

---

## resources/views/components/ui/

**Relevance:** Shared Blade components (button, card, input). The `index.blade.php` view should use these components for consistent UI. Check which components exist before building the view.

---

## resources/views/components/nav/admin-nav.blade.php

**Relevance:** The administrator navigation component. A "Configuración" link pointing to `route('configuracion.index')` must be added here so the admin can reach the feature from the main nav.
