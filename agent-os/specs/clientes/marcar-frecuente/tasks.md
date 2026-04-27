# Tasks: Marcar Cliente Frecuente

## Status Legend
- [ ] pending
- [x] done

---

## Task 1 — Docs (✅ completed)
[x] Write spec.md, tasks.md, shape.md, standards.md, references.md in `agent-os/specs/clientes/marcar-frecuente/`.

---

## Task 2 — Service Interface
[ ] Add `toggleFrecuente(Cliente $cliente): Cliente` to `app/Services/Clientes/Contracts/ClienteServiceInterface.php`.

---

## Task 3 — Service Implementation
[ ] Implement `toggleFrecuente` in `app/Services/Clientes/ClienteService.php`.
    - Flip `bFrecuente`, call `$cliente->save()`, return the updated model.
    - Use `declare(strict_types=1)` and readonly constructor.

---

## Task 4 — Route
[ ] Add to `routes/web.php` inside the `['auth', 'role:salesperson,administrator']` middleware group:
    ```php
    Route::patch('/clientes/{cliente}/frecuente', [ClienteController::class, 'toggleFrecuente'])
         ->name('clientes.toggleFrecuente');
    ```

---

## Task 5 — Controller Method
[ ] Add `toggleFrecuente(Cliente $cliente): JsonResponse` to `app/Http/Controllers/Web/Clientes/ClienteController.php`.
    - Inject `ClienteServiceInterface`; call `toggleFrecuente`; return `response()->json(['frecuente' => $cliente->bFrecuente])`.

---

## Task 6 — Badge Component
[ ] Create `resources/views/components/ui/badge-frecuente.blade.php`.
    - Renders a styled pill only when `$frecuente` is truthy.
    - Usage: `<x-ui.badge-frecuente :frecuente="$cliente->bFrecuente" />`.

---

## Task 7 — Toggle Button in Catalog Views
[ ] Add Alpine.js toggle button to:
    - `resources/views/salesperson/clientes/index.blade.php`
    - `resources/views/admin/clientes/index.blade.php`

    Each row gets `x-data="{ frecuente: {{ $cliente->bFrecuente ? 'true' : 'false' }} }"`.
    On click: optimistically toggle, PATCH endpoint, rollback + show error on failure.

---

## Task 8 — Badge in Sale Screen
[ ] Show `<x-ui.badge-frecuente>` next to the selected client's name in the sale/checkout view.
