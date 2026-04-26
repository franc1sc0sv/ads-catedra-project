---
name: Service Interface Pattern
description: Every service has a Contracts/ interface; controllers inject the interface, never the concrete class
type: project
---

# Service Interface Pattern

Every service class has a `Contracts/` interface alongside it. Controllers inject the interface, never the concrete class.

```
app/Services/Auth/
  AuthService.php
  Contracts/
    AuthServiceInterface.php
```

```php
// AppServiceProvider
$this->app->bind(AuthServiceInterface::class, AuthService::class);

// Controller
public function __construct(
    private readonly AuthServiceInterface $authService
) {}
```

**Why:** the interface explicitly documents the public contract and prevents callers from depending on internal methods.

- Business logic lives in the service, not the controller
- Controllers only do: validated input → service call → return response
- New services must follow the same `Contracts/` layout
