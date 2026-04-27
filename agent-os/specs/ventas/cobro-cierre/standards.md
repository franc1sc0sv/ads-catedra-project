# Standards — Cobro y Cierre

## authentication/role-middleware

Use `role:<value>` middleware on routes — never check roles inside controllers. `EnsureRole` reads `auth()->user()->role->value`. For this feature: `role:salesperson`.

---

## authentication/session-auth

Laravel session auth for everything. No JWT, no parallel API stack. Web routes use the `auth` middleware and the default Laravel session guard. Controllers return `View|RedirectResponse`.

---

## backend/php-architecture

Request flow is strictly: Route → FormRequest (validation) → Controller (thin) → ServiceInterface (injected) → Service (business logic) → Model (persistence). Controllers only do: validated input → service call → return response.

---

## backend/service-interface

Every service has a `Contracts/` interface alongside it. Controllers inject the interface, never the concrete class. Bindings live in `AppServiceProvider`. Layout:

```
app/Services/Ventas/
  VentaService.php
  Contracts/
    VentaServiceInterface.php
```

---

## frontend/role-namespacing

Views and nav components are namespaced by role. For this feature, all Blade templates live under `resources/views/salesperson/ventas/`.
