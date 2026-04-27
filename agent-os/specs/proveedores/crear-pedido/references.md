# References: Crear Pedido

## Codebase

- **`app/Http/Controllers/Web/Auth/AuthController.php`** — Patrón de controlador delgado: recibe FormRequest, delega al service vía interfaz, retorna `View|RedirectResponse`. Replicar para `PedidoController`.
- **`app/Services/Auth/Contracts/AuthServiceInterface.php`** — Patrón de interfaz de servicio en `Contracts/`. Replicar para `app/Services/Proveedores/Contracts/PedidoServiceInterface.php`.
- **`app/Providers/AppServiceProvider.php`** — Lugar donde se enlaza la interfaz al concrete. Agregar binding `PedidoServiceInterface => PedidoService`.
- **`app/Enums/UserRole.php`** — Patrón de enum backed con `label()`. Replicar en `EstadoPedido`.
- **`routes/web.php`** — Grupo `middleware(['auth', 'role:<rol>'])`. Agregar grupo `role:inventory_manager` para las rutas de pedidos.
- **`resources/views/layouts/app.blade.php`** — Layout autenticado base.
- **`resources/views/components/nav/inventory-manager-nav.blade.php`** — Nav del rol; agregar enlace a "Crear pedido".

## Producto

- **MVP Sección 4 — Proveedores y Pedidos** — Define el alcance: armar pedido, líneas, total, observaciones, fecha esperada, estados, locking por estado.

## Estándares aplicables (ver `standards.md`)

- authentication/role-middleware
- authentication/session-auth
- backend/php-architecture
- backend/service-interface
- frontend/role-namespacing
