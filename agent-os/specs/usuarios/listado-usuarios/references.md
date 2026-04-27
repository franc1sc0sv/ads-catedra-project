# References — Listado de Usuarios

## Codebase

### `app/Http/Controllers/Web/Auth/AuthController.php`
Patrón de controller delgado a seguir: validación → llamada a service → `View|RedirectResponse`. Además es el punto exacto donde se actualizará `fUltimoAcceso` tras login exitoso.

### `app/Models/User.php`
Modelo base sobre el que se aplican search + filtros. Aquí se agrega `fUltimoAcceso` a `fillable` y al método `casts()` (Laravel 12 style, no `$casts` property).

### `app/Enums/UserRole.php`
Enum backed con los valores `administrator`, `salesperson`, `inventory_manager`, `pharmacist`. Provee `label()` para mostrar el rol en la tabla y poblar el `<select>` de filtro de rol.

### `app/Http/Middleware/EnsureRole.php`
Middleware referenciado por `role:administrator` en la ruta. Lee `auth()->user()->role->value`. No se modifica para esta feature.

### `app/Providers/AppServiceProvider.php`
Punto de registro del binding `UsuarioServiceInterface => UsuarioService` dentro de `register()`.

### `routes/web.php`
Donde se registra la ruta `GET /admin/usuarios` dentro del grupo `auth` + `role:administrator`.

### `resources/views/layouts/app.blade.php`
Layout base que extiende la vista del listado.

### `resources/views/components/nav/admin-nav.blade.php`
Nav del rol administrator; debe incluir el link al listado de usuarios.

### `resources/views/components/ui/`
Componentes compartidos reutilizables: `button.blade.php`, `card.blade.php`, `input.blade.php`. Usar para search input, selects de filtro, y botones de acción por fila.

## Standards

- `agent-os/standards/authentication/role-middleware.md` — middleware-only role checks
- `agent-os/standards/authentication/session-auth.md` — session auth + `fUltimoAcceso` tracking
- `agent-os/standards/backend/php-architecture.md` — Route → Controller → ServiceInterface → Service → Model
- `agent-os/standards/backend/service-interface.md` — `Contracts/` directory + AppServiceProvider binding
- `agent-os/standards/frontend/role-namespacing.md` — `resources/views/admin/usuarios/`

## External

- Laravel 12 docs: pagination (`paginate`, `withQueryString`), validated requests, query scopes — consult via Context7 if needed.
- PostgreSQL `ILIKE` for case-insensitive search over `name`/`email`.
