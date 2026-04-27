# Tasks - Alta de Usuario

- [x] **Task 1: Save spec documentation** (this folder created with spec.md, shape.md, standards.md, references.md, tasks.md)

- [x] **Task 2: Service contract + implementation**
  - Create `app/Services/Usuarios/Contracts/UsuarioServiceInterface.php` with `create(array $data): User`.
  - Create `app/Services/Usuarios/UsuarioService.php` implementing the interface; persists `User` with role enum and plain password (let the model cast hash it).
  - Bind interface to implementation in `app/Providers/AppServiceProvider.php`.

- [x] **Task 3: FormRequest**
  - Create `app/Http/Requests/Usuarios/CreateUsuarioRequest.php`.
  - `authorize()`: `auth()->user()?->role === UserRole::ADMINISTRATOR`.
  - Rules: `name` required string max 255; `email` required email unique on `users.email`; `password` required string min 8 confirmed; `role` required Rule::enum(UserRole::class).

- [x] **Task 4: Controller**
  - Create `app/Http/Controllers/Web/Usuarios/UsuarioController.php` with `readonly` constructor injecting `UsuarioServiceInterface`.
  - `create(): View` returns the form view.
  - `store(CreateUsuarioRequest $request): RedirectResponse` calls service, redirects to listado route with flash success.
  - Both methods return `View|RedirectResponse`.

- [x] **Task 5: Routes**
  - Register `GET /admin/usuarios/create` and `POST /admin/usuarios` inside the existing `auth` + `role:administrator` group in `routes/web.php`.
  - Named: `admin.usuarios.create`, `admin.usuarios.store`.

- [x] **Task 6: View**
  - Create `resources/views/admin/usuarios/create.blade.php` extending `layouts/app.blade.php`.
  - Fields: name, email, role (select with `UserRole` cases and `label()`), password, password_confirmation.
  - Use shared `components/ui/` form components; show per-field errors via `@error`; preserve input via `old()`.
