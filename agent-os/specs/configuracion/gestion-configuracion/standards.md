# Standards: Gestión de Configuración Global

## authentication/role-middleware

All routes for this feature must be wrapped in the administrator role middleware group. No inline role checks inside controllers.

```php
Route::middleware(['auth', 'role:administrator'])->group(function () {
    Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
    Route::patch('/configuracion/{clave}', [ConfiguracionController::class, 'update'])->name('configuracion.update');
});
```

`EnsureRole` reads `auth()->user()->role->value`. Role enforcement takes effect on the next request after a role change.

---

## backend/php-architecture

File locations for this feature:

| Artifact | Path |
|---|---|
| Controller | `app/Http/Controllers/Web/Configuracion/ConfiguracionController.php` |
| Service | `app/Services/Configuracion/ConfiguracionService.php` |
| Interface | `app/Services/Configuracion/Contracts/ConfiguracionServiceInterface.php` |
| Model | `app/Models/CatConfiguracion.php` |
| Form Request | `app/Http/Requests/Configuracion/UpdateConfiguracionRequest.php` |
| View | `resources/views/admin/configuracion/index.blade.php` |

All PHP files must have `declare(strict_types=1)` as the first statement after `<?php`.

---

## backend/service-interface

The interface contract:

```php
<?php

declare(strict_types=1);

namespace App\Services\Configuracion\Contracts;

use Illuminate\Support\Collection;

interface ConfiguracionServiceInterface
{
    public function getValue(string $key, mixed $default = null): mixed;

    public function update(string $key, mixed $value): void;

    public function allEditable(): Collection;
}
```

The concrete `ConfiguracionService` injects dependencies via readonly constructor promotion. Binding is registered in `AppServiceProvider`:

```php
$this->app->bind(
    ConfiguracionServiceInterface::class,
    ConfiguracionService::class,
);
```

Controllers inject the interface, never the concrete class.

---

## frontend/role-namespacing

Views are namespaced by role. All views for administrator-only features live under:

```
resources/views/admin/
```

This feature's view: `resources/views/admin/configuracion/index.blade.php`

Shared UI components live under `resources/views/components/ui/` (button, card, input). Use them where they exist. Nav components live under `resources/views/components/nav/`; add the Configuración link to `admin-nav.blade.php`.

---

## General PHP Conventions (from CLAUDE.md)

- `declare(strict_types=1)` on every PHP file.
- Readonly constructor promotion for injected dependencies.
- `match` over `switch` for all branching.
- `casts()` method (not `$casts` property) — Laravel 12 style.
- Backed enums with a `label()` helper method (apply to `eTipoDato` enum if extracted to a PHP enum).
- Web controllers return only `View|RedirectResponse`.
- Business logic belongs in the service, not the controller.
