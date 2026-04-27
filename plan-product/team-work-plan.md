# FarmaSys — Team Work Plan (por rol / workflow)

> Asignación **centralizada por rol de usuario**. Cada persona aprende y entrega el workflow completo de un rol del sistema.
> Total: **33 features** en 9 dominios. Stack: Laravel 12 + Blade + Tailwind v4 + Alpine + PostgreSQL.

---

## 1. Por qué reparto por rol y no por feature

El sistema tiene **4 roles de usuario** (`administrator`, `salesperson`, `inventory_manager`, `pharmacist`). En vez de partir las features sueltas, cada dev se vuelve **dueño del workflow completo** de un rol. Beneficios:

- **Aprendizaje**: cada dev entiende un rol end-to-end (modelos, vistas, flujos, edge cases) en vez de tener pedazos sueltos.
- **Bajo contexto compartido**: no hay 2 personas tocando el mismo Blade o el mismo controller.
- **Menos conflictos en `routes/web.php`**: cada rol tiene su grupo de rutas, dueño único.
- **Demos limpias**: cada dev puede demostrar "su" usuario de principio a fin.

---

## 2. Composición del equipo


| Rol equipo         | Workflow asignado                                            | Features         | Disponibilidad |
| ------------------ | ------------------------------------------------------------ | ---------------- | -------------- |
| **TL** (yo)        | Foundations + Auth + Administrador + Encargado de Inventario | 17 + scaffolding | Full           |
| **Focus A (FA)**   | Vendedor / Cajero (POS)                                      | 9                | Full           |
| **Focus B (FB)**   | Farmacéutico                                                 | 3                | Full           |
| **Partial 1 (P1)** | Reportes operativos                                          | 2                | Parcial        |
| **Partial 2 (P2)** | Auditoría y movimientos                                      | 2                | Parcial        |
|                    |                                                              | **33**           |                |


---

## 3. Workflows en detalle

### 3.1 TL — Foundations + Auth + Administrador + Encargado de Inventario

**Doble workflow del usuario:**

> *Como Admin:* "Entro al sistema, gestiono usuarios y roles, ajusto la configuración global."
>
> *Como Encargado:* "Mantengo el catálogo de medicamentos, recibo alertas de stock bajo o vencimientos, gestiono proveedores, creo y recibo órdenes de compra. Recibir mercancía actualiza el stock automáticamente."

**Features (17):**


| Bloque    | Dominio       | Spec                    | Tamaño |
| --------- | ------------- | ----------------------- | ------ |
| Auth      | auth          | `login-web`             | M      |
| Auth      | auth          | `logout`                | S      |
| Auth      | auth          | `middleware-roles`      | M      |
| Admin     | configuracion | `gestion-configuracion` | M      |
| Admin     | usuarios      | `alta-usuario`          | S      |
| Admin     | usuarios      | `listado-usuarios`      | M      |
| Admin     | usuarios      | `edicion-rol`           | M      |
| Admin     | usuarios      | `cambiar-password`      | M      |
| Admin     | usuarios      | `activar-desactivar`    | M      |
| Encargado | inventario    | `catalogo-medicamentos` | M      |
| Encargado | inventario    | `alertas-stock`         | M      |
| Encargado | inventario    | `ajuste-stock`          | M      |
| Encargado | inventario    | `historial-movimientos` | M      |
| Encargado | proveedores   | `catalogo-proveedores`  | S      |
| Encargado | proveedores   | `crear-pedido`          | M      |
| Encargado | proveedores   | `listado-pedidos`       | S      |
| Encargado | proveedores   | `recibir-pedido`        | M      |


**Además entrega scaffolding cross-cutting:**

- Migrations + seeders de las 11 tablas del DBML.
- `app/Services/<dominio>/Contracts/` para todos los dominios (interfaces que los demás consumen).
- `layouts/app.blade.php` + `layouts/auth.blade.php`.
- `components/ui/`* reusables (button, card, input, table, alert, badge, modal).
- `components/nav/<role>-nav.blade.php` (los 4).
- `EnsureRole` middleware + matriz de permisos.
- Patrón de transacción atómica (lo deja documentado y demostrado en `recibir-pedido` y `cobro-cierre` para que FA y FB lo repliquen).
- Hooks de auditoría que P2 consume en `bitacora-auditoria`.
- `routes/web/<dominio>.php` por dominio.

**Hito técnico:** `recibir-pedido` y `cobro-cierre` definen el patrón transaccional del sistema (estado + stock + movimientos atómicos).

**% del trabajo:** ~52% por feature-count + scaffolding cross-cutting.

---

### 3.2 Focus A — Vendedor / Cajero (POS)

**Workflow del usuario:**

> "Soy cajero. Abro una venta, busco al cliente (o lo creo en el modal), agrego productos. Si hay un controlado capturo la receta. Cobro y cierro la venta — el stock se descuenta solo. Si me equivoqué, cancelo."

**Features (9):**


| Dominio  | Spec                | Tamaño |
| -------- | ------------------- | ------ |
| ventas   | `nueva-venta`       | M      |
| ventas   | `cobro-cierre`      | M      |
| ventas   | `adjuntar-receta`   | M      |
| ventas   | `cancelar-venta`    | M      |
| clientes | `catalogo-clientes` | M      |
| clientes | `busqueda-venta`    | M      |
| clientes | `marcar-frecuente`  | S      |
| clientes | `historial-compras` | M      |
| recetas  | `registro-receta`   | M      |


**Hito técnico:** `cobro-cierre` — atomicidad estricta (estado venta + decremento stock + creación de movimientos + check de receta validada en controlados). **La transacción más crítica del sistema**. TL pair-programa la primera versión.

**Demo de cierre:** "cajero abre venta → busca cliente frecuente → agrega 3 productos (uno controlado) → captura receta → FB la valida desde su pantalla → cajero cobra → recibo impreso, stock bajó, movimientos creados."

---

### 3.3 Focus B — Farmacéutico

**Workflow del usuario:**

> "Soy farmacéutico. Veo la cola de recetas pendientes ordenadas por antigüedad, abro una, valido o rechazo con notas, y consulto mi archivo histórico de revisiones."

**Features (3):**


| Dominio | Spec                | Tamaño |
| ------- | ------------------- | ------ |
| recetas | `cola-pendientes`   | M      |
| recetas | `validar-rechazar`  | M      |
| recetas | `historial-recetas` | M      |


**Hito técnico:** `validar-rechazar` — lock pesimista para que dos farmacéuticos no validen la misma receta a la vez. Decisión inmutable con auditoría. **El reto técnico más sutil del proyecto**.

**Demo de cierre:** "farmacéutico ve cola → abre receta más vieja → la valida con nota → la venta del cajero se desbloquea automáticamente → archivo histórico muestra la decisión."

> FB tiene menos features pero tiene un reto técnico exigente. Si avanza rápido puede tomar `bitacora-auditoria` como overflow desde P2.

---

### 3.4 Partial 1 — Reportes operativos

**Workflow del usuario:**

> "Soy admin. Necesito ver KPIs de ventas con filtros de fecha y exportar a CSV. También snapshot del inventario actual con valor total y productos por vencer."

**Features (2):**


| Dominio  | Spec                 | Tamaño |
| -------- | -------------------- | ------ |
| reportes | `reporte-ventas`     | M      |
| reportes | `reporte-inventario` | M      |


**Por qué partial:** ambas son **read-only** sobre tablas que ya existen. Sin riesgo de romper transacciones. Patrón de exportación CSV reusable. P1 puede trabajar al ~30% de tiempo.

---

### 3.5 Partial 2 — Auditoría y movimientos

**Workflow del usuario:**

> "Soy admin. Necesito el log inmutable de movimientos de stock (qué entró, qué salió, quién, cuándo) y la bitácora de auditoría del sistema (logins, CRUD usuarios, validaciones de receta, ventas canceladas)."

**Features (2):**


| Dominio  | Spec                  | Tamaño |
| -------- | --------------------- | ------ |
| reportes | `reporte-movimientos` | S      |
| reportes | `bitacora-auditoria`  | M      |


**Hito técnico:** `bitacora-auditoria` — implementa el listener que captura los eventos que el TL emite desde sus hooks. P2 se vuelve experto del sistema completo viendo *qué* genera cada acción.

**Por qué partial:** read-only, sin bloquear a nadie. Excelente onboarding integral con bajo riesgo.

---

## 4. Secuencia temporal

```
Sem 1-3  TL solo: scaffolding + auth + configuracion + usuarios + inventario base
            └─> al final: migrations corridas, layouts listos, middleware operando,
                catalogo-medicamentos y alertas-stock funcionando

Sem 4-5  TL: ajuste-stock + historial-movimientos + proveedores
         FA arranca: catalogo-clientes + nueva-venta (skeleton)
         FB arranca: cola-pendientes (mock data hasta que FA cree Recetas reales)

Sem 6-7  TL: recibir-pedido (cierra encargado)
         FA: cobro-cierre + adjuntar-receta + registro-receta
         FB: validar-rechazar
         P1 arranca: reporte-ventas

Sem 8-9  FA: cancelar-venta + busqueda-venta + marcar-frecuente + historial-compras
         FB: historial-recetas
         P1: reporte-inventario
         P2 arranca: reporte-movimientos + bitacora-auditoria

Sem 10   TL: review final, polish
         Todos: UAT cruzado por rol
```

**Critical path:** TL semanas 1-3 (bloquea a todos), luego FA hasta sem 7 (recetas que FB necesita), luego cierre paralelo.

---

## 5. Sync points obligatorios


| Cuándo       | Quiénes    | Tema                                                                                                                        |
| ------------ | ---------- | --------------------------------------------------------------------------------------------------------------------------- |
| Final sem 3  | TL → todos | **Kickoff técnico**. Demo de auth + scaffolding + inventario base. Cada dev clona, corre `composer dev`, loguea con su rol. |
| Inicio sem 4 | TL ↔ FA    | Pair de 1h sobre `cobro-cierre`: estado de la venta, atomicidad, eventos. Replica del patrón de `recibir-pedido`.           |
| Inicio sem 5 | FA → FB    | FA demuestra cómo se crea una `Receta` desde POS. FB ya puede mockear datos para `cola-pendientes`.                         |
| Inicio sem 6 | TL ↔ FB    | Walkthrough del lock pesimista para `validar-rechazar`.                                                                     |
| Inicio sem 8 | TL ↔ P2    | TL pasa el contrato de `AuditoriaService` y los eventos que ya emite.                                                       |
| Cada viernes | Todos      | Standup técnico 30min + demo de lo mergeado.                                                                                |


---

## 6. Reglas de colaboración

- **1 spec = 1 PR**. PRs grandes se rechazan.
- **Cada dev escribe en sus dominios**, no toca los de otros. Si necesita cambiar un dominio ajeno, abre issue al dueño.
- **Routes**: cada dominio tiene `routes/web/<dominio>.php`. Cero conflictos en `routes/web.php`.
- **UI**: nadie escribe componentes sueltos. Si hace falta uno nuevo en `components/ui/`, lo pide al TL.
- **Tests**: cada feature lleva mínimo 1 test happy-path + 1 edge case (Pest).
- **Code review**: TL revisa todo. FA y FB pueden cross-revisar entre sí. P1 y P2 reciben review del TL.

---

## 7. Onboarding por dev (día 1)

1. Clonar repo, `docker compose up -d`, `composer install && npm install`, `php artisan migrate --seed`, `composer dev`.
2. Loguear con la cuenta seed de su rol asignado:
  - FA → `sales@pharma.test`
  - FB → `pharmacist@pharma.test`
  - P1 / P2 → `admin@pharma.test`
3. Leer en orden:
  - `CLAUDE.md` (convenciones)
  - `agent-os/standards/`* (estándares)
  - `plan-product/database-schema.dbml` (modelo)
  - `plan-product/product-roadmap.md` (visión)
  - Los specs de **su workflow** en `agent-os/specs/<dominio>/<slug>/spec.md`
4. Pair de 1h con TL para clarificar criterios de aceptación.
5. Primer PR: el feature **S** o **M más simple** del workflow.

---

## 8. Resumen ejecutivo


| Persona       | Workflow / Rol                                                                           | Features         | %    |
| ------------- | ---------------------------------------------------------------------------------------- | ---------------- | ---- |
| **TL (yo)**   | Foundations + Auth + Administrador + Encargado de Inventario + scaffolding cross-cutting | 17 + scaffolding | ~52% |
| **Focus A**   | Vendedor / Cajero (ventas + clientes + receta capture)                                   | 9                | ~27% |
| **Focus B**   | Farmacéutico (cola + validación + historial)                                             | 3                | ~9%  |
| **Partial 1** | Reportes operativos (ventas + inventario)                                                | 2                | ~6%  |
| **Partial 2** | Auditoría y movimientos (movimientos + bitácora)                                         | 2                | ~6%  |


**Cada persona aprende y entrega un workflow cerrado.** Cero overlap de dominios. TL adelanta las bases; FA, FB, P1, P2 corren en paralelo a partir de la semana 4.