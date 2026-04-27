# Standards: login-web

## authentication/session-auth

Una sola pila de autenticación: sesiones de Laravel para todas las rutas.
- routes/web.php → middleware `auth` → guard de sesión por defecto.
- No hay routes/api.php ni middleware jwt.auth.

Login flow: Auth::attempt($credentials) + $request->session()->regenerate() + redirect to role dashboard via redirectPathAfterLogin().
Logout flow: Auth::logout() + session()->invalidate() + session()->regenerateToken().

Rules:
- Controllers retornan View|RedirectResponse — no JsonResponse.
- Nunca hashear contraseñas a mano: Hash::make() y cast 'password' => 'hashed' en el modelo.

Route examples:
```php
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
```

---

## authentication/role-middleware

Use `role:<value>` middleware to guard routes:
```php
Route::middleware(['auth', 'role:administrator'])->group(...);
```
- El middleware lee $request->user()?->role?->value en cada request.
- Si no coincide, devuelve 403.
- Nunca chequear el rol dentro de un controller — siempre vía middleware en la ruta.
- Roles: administrator, salesperson, inventory_manager, pharmacist

---

## backend/php-architecture

Request lifecycle: Route → FormRequest → Controller → ServiceInterface → Service → Model → Response

File placement:
- Web controller: app/Http/Controllers/Web/[Domain]/[Name]Controller.php
- Service:        app/Services/[Domain]/[Name]Service.php
- Interface:      app/Services/[Domain]/Contracts/[Name]ServiceInterface.php
- Form request:   app/Http/Requests/[Domain]/[Name]Request.php

PHP 8.x: readonly constructor promotion, match over switch, casts() method, null-safe operator.

---

## backend/service-interface

Every service has a Contracts/ interface. Controllers inject the interface, never the concrete class.

```php
// AppServiceProvider
$this->app->bind(AuthServiceInterface::class, AuthService::class);
// Controller
public function __construct(private readonly AuthServiceInterface $authService) {}
```

---

## frontend/role-namespacing

Every role owns a full slice: routes, controller, views, nav.
Views: resources/views/{role}/{domain}/{view}.blade.php
x-ui.* components are shared; x-nav.* are role-specific.
