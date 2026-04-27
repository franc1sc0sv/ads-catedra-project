# References: busqueda-venta

## AuthController — thin controller pattern

`app/Http/Controllers/Web/Auth/AuthController.php`

Demonstrates the expected controller shape: inject service interface via readonly constructor, validate input through FormRequest, delegate all logic to the service, return `View|RedirectResponse`. No business logic lives in the controller. `ClienteBusquedaController` follows the same pattern, returning `JsonResponse` instead of a view for its two JSON endpoints.

## AuthServiceInterface — interface pattern

`app/Services/Auth/Contracts/AuthServiceInterface.php`

Shows how service contracts are declared alongside their implementation under a `Contracts/` subdirectory. The interface defines only the public methods the controller needs; implementation details stay in the concrete class. `ClienteServiceInterface` mirrors this structure at `app/Services/Clientes/Contracts/ClienteServiceInterface.php`.
