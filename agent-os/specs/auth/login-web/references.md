# References: login-web

## app/Http/Controllers/Web/Auth/AuthController.php

**Relevance:** Canonical thin controller for this feature. Uses readonly constructor injection of `AuthServiceInterface`. Returns `View|RedirectResponse` only. This is the file to update with `showLogin()`, `login(LoginRequest)`, and `logout(Request)` methods. The existing controller uses JWT (legacy) — replace only the auth logic with `Auth::attempt()` + session; keep the file path and class structure intact.

---

## app/Services/Auth/Contracts/AuthServiceInterface.php

**Relevance:** Defines the service contract the controller depends on. Already declares `redirectPathAfterLogin()`. Verify the return type is `string`. No changes expected unless the signature is missing or incorrect. Controllers must inject this interface, never `AuthService` directly.

---

## app/Services/Auth/AuthService.php

**Relevance:** Concrete implementation of `AuthServiceInterface`. Contains the `redirectPathAfterLogin()` method with a `match` expression over `UserRole` values — this pattern is correct and must be preserved/implemented. Ignore any JWT-related methods; they are legacy and out of scope for this feature. The role-to-path mapping lives here: `administrator` → admin dashboard, `salesperson` → salesperson dashboard, `inventory_manager` → inventory manager dashboard, `pharmacist` → pharmacist dashboard.
