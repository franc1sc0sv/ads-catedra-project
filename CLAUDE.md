# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Stack

PHP 8.2+, Laravel 12, Blade, Tailwind CSS v4, Alpine.js (CDN), Vite, PostgreSQL 16 (Docker).

## Commands

```bash
# Start DB (required before anything else)
docker compose up -d

# Dev server (PHP + queue + Vite, all in one)
composer dev

# Run all tests
composer test

# Run a single test file
php artisan test tests/Feature/ExampleTest.php

# Code style (Laravel Pint)
./vendor/bin/pint

# Build frontend
npm run build
```

## Architecture

### Authentication

Sesiones de Laravel para todo. Sin JWT, sin stack API paralelo.

| Layer | Middleware | Guard |
|---|---|---|
| `routes/web.php` | `auth` | Laravel session |

Web controllers return `View|RedirectResponse`. La sesión vive en la tabla `sessions` que crea Laravel — no se modela en el DBML del proyecto.

### Service Interface Pattern

Every service has a `Contracts/` interface alongside it. Controllers inject the interface, never the concrete class. Bindings live in `AppServiceProvider`.

```
app/Services/Auth/
  AuthService.php
  Contracts/
    AuthServiceInterface.php
```

Controllers only do: validated input → service call → return response. Business logic belongs in the service.

### Role Authorization

Roles are defined in `App\Enums\UserRole`: `administrator`, `salesperson`, `inventory_manager`, `pharmacist`.

Use `role:<value>` middleware on routes — never check roles inside controllers:

```php
Route::middleware(['auth', 'role:administrator'])->group(...);
```

`EnsureRole` reads `auth()->user()->role->value`. Cambiar el rol toma efecto en el siguiente request del usuario.

### Controller Namespacing

```
app/Http/Controllers/
  Web/Auth/AuthController.php       ← session-based, returns views
  Web/Dashboard/AdminController.php
  Web/Dashboard/SalespersonController.php
  Web/Dashboard/InventoryManagerController.php
  Web/Dashboard/PharmacistController.php
```

### Views

Views and nav components are namespaced by role:

```
resources/views/
  admin/dashboard/
  salesperson/dashboard/
  inventory-manager/dashboard/
  pharmacist/dashboard/
  components/nav/<role>-nav.blade.php
  components/ui/         ← shared button, card, input
  layouts/app.blade.php
  layouts/auth.blade.php
```

## PHP Conventions

All files use `declare(strict_types=1)`. Key patterns:

- Readonly constructor promotion for injected dependencies
- `match` over `switch`
- `casts()` method (not `$casts` property) — Laravel 12 style
- Backed enums with a `label()` helper method

## Available Skills

Invoke with `/skill-name` in the prompt.

### Laravel / PHP

| Skill | When to use |
|---|---|
| `/laravel-specialist` | Eloquent models/relationships, Sanctum, Horizon queues, Livewire, API resources, Pest/PHPUnit tests |
| `/laravel-patterns` | Service layers, routing/controllers, events, caching, queues, API resource design |
| `/php-pro` | PHP 8.3+ features, strict typing, PHPStan level 9, DTOs, value objects, PSR standards |
| `/laravel-cloud:deploying-laravel-cloud` | Deploy/manage the app on Laravel Cloud (environments, DBs, domains, background workers) |

### Frontend

| Skill | When to use |
|---|---|
| `/tailwind-css-patterns` | Tailwind v4 utilities, responsive layouts, flexbox/grid, design systems |
| `/frontend-design` | Building polished Blade components, landing pages, dashboards with high design quality |
| `/vite` | `vite.config.js` changes, plugin API, SSR, library builds |
| `/accessibility` | WCAG 2.2 audit, screen reader support, keyboard navigation |

### Code Quality

| Skill | When to use |
|---|---|
| `/core-coding-standards` | General code review — KISS, DRY, clean code |
| `/simplify` | After a change: review for reuse, quality, and efficiency |
| `/review` | Review a pull request |
| `/security-review` | Security audit of pending branch changes |
| `/ccc-skills:uat-testing` | End-to-end UAT via Playwright before merging a feature branch |

### Diagrams & Docs

| Skill | When to use |
|---|---|
| `/ccc-skills:excalidraw` | Generate architecture diagrams as `.excalidraw` files from codebase analysis |
| `/project-documenter` | Generate app walkthrough, feature inventory, or onboarding docs with screenshots |

### Project / Claude Config

| Skill | When to use |
|---|---|
| `/agent-os:discover-standards` | View the agent-os standards index for this project |
| `/agent-os:shape-spec` | Shape a spec for a new feature before implementation |
| `/claude-md-management:revise-claude-md` | Update this file with learnings from the current session |
| `/claude-md-management:claude-md-improver` | Audit and improve all CLAUDE.md files in the repo |
| `/fewer-permission-prompts` | Reduce repetitive permission prompts by updating `.claude/settings.json` |

## Database & Seeded Accounts

PostgreSQL via Docker. After `php artisan migrate --seed`:

| Email | Role |
|---|---|
| admin@pharma.test | administrator |
| sales@pharma.test | salesperson |
| inventory@pharma.test | inventory_manager |
| pharmacist@pharma.test | pharmacist |
