# References — Bitácora de Auditoría

## Codebase

- `app/Http/Controllers/Web/Auth/AuthController.php` — Thin controller pattern reference. Ilustra el flujo `validated input → service call → return View|RedirectResponse` que `BitacoraController` y los call-sites de `log()` deben seguir. También es el primer call-site donde inyectar `BitacoraServiceInterface` (LOGIN_OK / LOGIN_FAIL / LOGOUT).

## Product context

- MVP Section 8: **Reportes y Auditoría** — Origen funcional de esta feature; define las 9 acciones a registrar y la regla de acceso solo-administrador.

## Cross-cutting constraints

- `declare(strict_types=1)` en todo archivo PHP.
- Readonly constructor promotion para `BitacoraService` y para los controladores que inyecten `BitacoraServiceInterface`.
- Web-only — no API parallel stack; las vistas devuelven `View|RedirectResponse`.
- `role:administrator` corta antes de leer la bitácora (403 para otros roles).
