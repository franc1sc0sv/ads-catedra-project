# Tasks — Listado de Usuarios

- [x] Task 1: Save spec documentation (`spec.md`, `tasks.md`, `shape.md`, `standards.md`, `references.md`)

## Implementation

- [x] Task 2: Add `fUltimoAcceso` column
  - Create migration `add_fultimoacceso_to_users_table` with `timestamp('fUltimoAcceso')->nullable()`
  - Add `fUltimoAcceso` to `User::$fillable` (or unguarded) and to `casts()` as `'datetime'`
  - In `app/Http/Controllers/Web/Auth/AuthController.php`, after a successful `Auth::attempt()`, persist `auth()->user()->forceFill(['fUltimoAcceso' => now()])->save()`

- [x] Task 3: Service contract + implementation
  - Create `app/Services/Usuarios/Contracts/UsuarioServiceInterface.php` with `list(array $filters): LengthAwarePaginator`
  - Create `app/Services/Usuarios/UsuarioService.php` implementing it; apply ILIKE search over `name` + `email`, filter by `role` (UserRole enum) and `is_active` (bool), `paginate(15)->withQueryString()`
  - Bind interface to concrete in `App\Providers\AppServiceProvider::register()`

- [x] Task 4: Controller
  - Create `app/Http/Controllers/Web/Usuarios/UsuarioController.php` with `index(Request $request): View`
  - Validate query: `search?string`, `role?in:UserRole values`, `estado?in:activos,inactivos,todos`
  - Inject `UsuarioServiceInterface` via readonly constructor
  - Return `view('admin.usuarios.index', compact('usuarios', 'filters'))`

- [x] Task 5: Routes
  - In `routes/web.php`, under the `auth` + `role:administrator` group, register `Route::get('/admin/usuarios', [UsuarioController::class, 'index'])->name('admin.usuarios.index')`

- [x] Task 6: View
  - Create `resources/views/admin/usuarios/index.blade.php` extending `layouts/app.blade.php`
  - Include search input, role select, estado select; submit via GET preserving values
  - Render table with columns: Nombre, Correo, Rol (`$user->role->label()`), Estado, Último acceso, Acciones
  - Each row: edit link, change-password link, toggle estado form (POST)
  - Empty-state block (two variants: filtered-no-results vs no-users)
  - Render `{{ $usuarios->withQueryString()->links() }}`

- [x] Task 7: Verify access control
  - Hit `/admin/usuarios` as each non-administrator role; expect 403/redirect
  - Hit as administrator; expect 200 with table

- [ ] Task 8: Code style
  - Run `./vendor/bin/pint`
  - Run `composer test`
