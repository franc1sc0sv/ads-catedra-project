# Standards — Adjuntar Receta

## authentication/role-middleware

Aplicar autorización mediante middleware `role:<value>` en las rutas, nunca chequear roles dentro del controlador. Para esta feature, todas las rutas de captura/re-vinculación van bajo `role:salesperson`. La validación de receta (que es una feature aparte) iría bajo `role:pharmacist`.

---

## authentication/session-auth

Autenticación por sesiones de Laravel. Sin JWT, sin stack API paralelo. Las rutas viven en `routes/web.php` bajo middleware `auth`. Los controladores devuelven `View|RedirectResponse`.

---

## backend/php-architecture

Flujo: Route → FormRequest → Controller → ServiceInterface → Service → Model. El controlador se limita a: input validado del FormRequest → llamada al servicio → respuesta. La lógica de negocio (4 condiciones de re-vinculación, compuerta de cobro, transacción de attach) vive en el servicio.

---

## backend/service-interface

Cada servicio tiene su interfaz en `Contracts/`. Los controladores inyectan la interfaz, nunca la clase concreta. Los bindings se registran en `AppServiceProvider`. Para esta feature:

- `App\Services\Ventas\Contracts\VentaServiceInterface` ← `App\Services\Ventas\VentaService`
- `App\Services\Recetas\Contracts\RecetaServiceInterface` ← `App\Services\Recetas\RecetaService`

---

## frontend/role-namespacing

Las vistas se organizan por rol. La pantalla de adjuntar receta vive en `resources/views/salesperson/ventas/adjuntar-receta.blade.php`. Usar `layouts/app.blade.php` y el componente de navegación `components/nav/salesperson-nav.blade.php`. Componentes UI compartidos (botón, card, input) desde `components/ui/`.
