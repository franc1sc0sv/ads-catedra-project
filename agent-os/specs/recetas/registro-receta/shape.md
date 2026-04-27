# Shape — Registro de Receta

## Decisiones de diseño

### `cveMedicamento` desde el carrito, no desde el cliente
El `cveMedicamento` que ampara cada receta **se resuelve en el servidor** a partir del estado del carrito de la venta en curso. El formulario no lo expone como input ni el `FormRequest` lo acepta. Esto evita que un cajero (o un cliente manipulando el request) vincule una receta a un medicamento distinto al que se está vendiendo.

### Número de receta único global
`nNumeroReceta` lleva un índice unique global sobre toda la tabla `recetas`. No es unique por venta ni por médico — es unique en el sistema entero, porque representa un documento físico real cuyo folio no se repite. Esto implica que reutilizar una receta ya capturada en otra venta no es válido en este flujo (registrar = crear nueva).

### Receta vencida bloquea
Si `fVencimiento < today()` al momento de guardar, el servicio rechaza el registro con un error de dominio. No se permite guardar la receta como `PENDIENTE` para que luego sea rechazada en validación — se corta antes.

### Inserción atómica
`Receta` + `VentaReceta` se insertan dentro de una sola transacción. Si la inserción del pivote falla (p. ej. ya existe una receta para `(cveVenta, cveMedicamento)` por el unique compuesto), se hace rollback de la `Receta` también. No queda receta huérfana.

### El formulario se repite por cada controlado
La UI no captura todas las recetas en un solo paso. Cada submit registra una receta para un medicamento controlado. Si quedan más controlados sin receta en el carrito, el flujo vuelve a abrir el formulario con el siguiente. Esto simplifica el `FormRequest` (una receta por request) y evita transacciones largas con N inserciones.

### Estado inicial siempre `PENDIENTE`
El servicio fija `eEstado = PENDIENTE` al insertar; nunca confía en el cliente para el estado. La transición a `VALIDADA` / `RECHAZADA` ocurre en otra feature, ejecutada por farmacéutico, no por cajero.

## Riesgos y consideraciones

- **Carrito como fuente de verdad:** este flujo asume que existe una representación servidor-side del carrito de la venta en curso desde la cual obtener los `cveMedicamento` controlados pendientes. Si esa representación aún no existe, es prerequisito para esta feature.
- **Concurrencia en `nNumeroReceta`:** dos cajeros capturando el mismo número simultáneamente — el unique constraint de la base lo resuelve; el servicio debe traducir la violación a un error de dominio amigable.
- **Tipo de fecha de venta:** la comparación `fVencimiento >= today()` usa la fecha del servidor. No se modela aún zona horaria por sucursal.
