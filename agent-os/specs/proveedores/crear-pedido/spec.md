# Spec: Crear Pedido

## Resumen

El encargado de inventario arma un pedido nuevo a un proveedor activo, agregando una o más líneas de medicamento (cantidad y precio unitario estimado). Al guardar, el pedido se persiste junto con todas sus líneas en una única transacción de base de datos y nace en estado `SOLICITADO`, registrando usuario creador y fecha de creación.

Solo el rol `inventory_manager` tiene acceso a esta funcionalidad.

## Alcance funcional

- Selección de proveedor activo desde un combo.
- Adición dinámica de líneas: medicamento, cantidad, precio unitario estimado.
- Cálculo de subtotal por línea (`cantidad × precio unitario`) en el cliente; el total estimado del pedido se persiste como suma de subtotales.
- Restricción: un mismo medicamento solo puede aparecer una vez por pedido (constraint de base de datos).
- Campos opcionales: observaciones (texto libre) y fecha esperada de entrega.
- Persistencia atómica master-detail en `DB::transaction()`.
- Estado inicial fijo: `SOLICITADO`.
- El pedido es editable mientras esté en `SOLICITADO`. Pasa a bloqueado (no editable) al estar en `ENVIADO`, `RECIBIDO` o `CANCELADO`. La edición/cancelación posterior queda fuera de esta spec; aquí solo se cubre la creación.

## Modelo de datos

### Enum `EstadoPedido` (PHP backed enum, string)

- `SOLICITADO`
- `ENVIADO`
- `RECIBIDO`
- `CANCELADO`

### Tabla `pedidos`

| Columna | Tipo | Notas |
|---|---|---|
| `cvePedido` | PK | autoincrement |
| `cveProveedor` | FK proveedores | not null |
| `eEstado` | enum | default `SOLICITADO` |
| `nTotal` | decimal(12,2) | suma de subtotales de detalles |
| `cObservaciones` | text nullable | |
| `fEntregaEsperada` | date nullable | |
| `cveUsuarioCreador` | FK users | not null |
| `fCreado` | timestamp | |

### Tabla `detalle_pedidos`

| Columna | Tipo | Notas |
|---|---|---|
| `cveDetalle` | PK | autoincrement |
| `cvePedido` | FK pedidos | cascade on delete |
| `cveMedicamento` | FK medicamentos | not null |
| `nCantidad` | int | > 0 |
| `nPrecioUnitario` | decimal(10,2) | >= 0 |
| **unique** | (`cvePedido`, `cveMedicamento`) | constraint a nivel DB |

## Flujo de creación

1. `GET /proveedores/pedidos/create` (rol `inventory_manager`) renderiza el formulario master-detail.
2. `POST /proveedores/pedidos` recibe payload con cabecera + arreglo de líneas, validado por `CreatePedidoRequest`.
3. `PedidoController` invoca `PedidoServiceInterface::create($data, $userId)`.
4. El service abre `DB::transaction()`, crea el `Pedido` con `eEstado = SOLICITADO` y `nTotal` calculado, e inserta cada `DetallePedido`. La unique constraint (`cvePedido`, `cveMedicamento`) garantiza que no haya medicamentos duplicados aunque la validación de request fallara.
5. Redirige al listado/detalle con flash de éxito.

## Reglas de negocio

- Al menos una línea de detalle.
- Cantidad entera positiva; precio unitario decimal no negativo.
- Proveedor debe existir y estar activo.
- Cada medicamento de la lista debe existir y aparecer una sola vez por pedido.
- `nTotal` se calcula en el service, no se confía en el cliente.
- El estado se fija en el service; el cliente no lo envía.
