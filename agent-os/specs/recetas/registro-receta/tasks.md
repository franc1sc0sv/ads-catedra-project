# Tasks — Registro de Receta

- [x] **Task 1: Save spec documentation** (done)

- [ ] **Task 2: Migración `recetas`**
  - Agregar (o crear) los campos: `nNumeroReceta` (unique global), `cNombrePaciente`, `cNombreMedico`, `cCedulaMedico`, `fEmision`, `fVencimiento`, `eEstado` (enum, default `PENDIENTE`), `cveMedicamento` (FK a `medicamentos`).
  - Índice unique sobre `nNumeroReceta`.

- [ ] **Task 3: Modelo y migración `VentaReceta`**
  - Modelo `App\Models\VentaReceta` (o pivote con modelo) con `cveVenta`, `cveMedicamento`, `cveReceta`.
  - Migración con FKs y unique compuesto `(cveVenta, cveMedicamento)`.

- [ ] **Task 4: Servicio**
  - Agregar `registrar(...)` a `App\Services\Recetas\Contracts\RecetaServiceInterface`.
  - Implementación en `App\Services\Recetas\RecetaService` que dentro de una transacción:
    1. Valida que `nNumeroReceta` sea único global.
    2. Valida que `fVencimiento >= today()`; si está vencida, lanza excepción de dominio.
    3. Toma `cveMedicamento` del carrito asociado a la `Venta`, no del request.
    4. Inserta `Receta` con `eEstado = PENDIENTE`.
    5. Inserta `VentaReceta` `(cveVenta, cveMedicamento, cveReceta)`.
  - Registrar binding interface → concreta en `AppServiceProvider`.

- [ ] **Task 5: FormRequest**
  - `app/Http/Requests/Recetas/RegistrarRecetaRequest.php` con reglas para `nNumeroReceta`, `cNombrePaciente`, `cNombreMedico`, `cCedulaMedico`, `fEmision`, `fVencimiento`. **No** acepta `cveMedicamento` desde el body.

- [ ] **Task 6: Controller**
  - `app/Http/Controllers/Web/Recetas/RecetaController.php` con método `store` que recibe el `RegistrarRecetaRequest` + el contexto de `Venta` y `Medicamento` (resuelto en el servidor desde el carrito).
  - Devuelve `View|RedirectResponse`. Sin lógica de negocio en el controlador.

- [ ] **Task 7: Rutas**
  - En `routes/web.php`, registrar bajo `middleware(['auth', 'role:salesperson'])` la ruta `POST` para `RecetaController@store`.

- [ ] **Task 8: Vista embebida**
  - `resources/views/salesperson/ventas/receta-form.blade.php` — formulario embebido en el flujo de venta. Muestra el medicamento al que aplica (read-only), captura los campos y se envía a la ruta del controller. El formulario se repite por cada controlado pendiente.
