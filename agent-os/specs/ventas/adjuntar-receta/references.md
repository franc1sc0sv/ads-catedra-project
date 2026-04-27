# References — Adjuntar Receta

## Codebase

- `app/Http/Controllers/Web/Auth/AuthController.php` — Patrón de controlador delgado a replicar: FormRequest → service interface → `View|RedirectResponse`. `VentaController` debe seguir esta misma forma sin meter lógica de las 4 condiciones de re-vinculación ni del cálculo de `isCobrableAhora`.

- `app/Providers/AppServiceProvider.php` — Lugar donde registrar los bindings `VentaServiceInterface → VentaService` y `RecetaServiceInterface → RecetaService`.

- `app/Enums/UserRole.php` — Confirma los valores `salesperson` y `pharmacist` que se pasan al middleware `role:`.

- `routes/web.php` — Patrón de agrupación `Route::middleware(['auth', 'role:salesperson'])` para ubicar las nuevas rutas.

- `resources/views/layouts/app.blade.php` y `resources/views/components/nav/salesperson-nav.blade.php` — Layout y nav que la vista `adjuntar-receta.blade.php` debe extender.

## Producto

- Plan-product MVP, sección 6 (Ventas) y 7 (Recetas) — Origen del requisito regulatorio: cobro bloqueado hasta validación farmacéutica de cada controlado.

## Specs hermanas

- `agent-os/specs/ventas/` (otras specs del dominio) — Coordinar contratos públicos de `VentaServiceInterface`: este spec añade `attachReceta`, `attachExistingReceta`, `isCobrableAhora` que probablemente conviven con métodos de cobro/cancelación definidos en specs vecinas.

- Cualquier spec de "validar receta" bajo `recetas/` — Consume las recetas en estado PENDIENTE creadas por esta feature; el contrato es: la receta nace PENDIENTE en `attachReceta` y la otra spec la mueve a VALIDADA/RECHAZADA. La vinculación `VentaReceta` no cambia.
