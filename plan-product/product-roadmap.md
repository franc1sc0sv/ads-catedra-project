# Product Roadmap

Construimos de adentro hacia afuera: primero los cimientos que todo lo demás necesita (autenticación, usuarios, inventario), luego las operaciones diarias (ventas, recetas), y al final lo que solo se entiende cuando ya hay datos circulando (reportes y auditoría). Cada sección queda usable antes de empezar la siguiente.

## MVP — What ships first

El primer release entrega una farmacia funcionando de punta a punta. Desde el día uno, el administrador puede crear usuarios y asignarles rol; el encargado puede cargar el catálogo de medicamentos, registrar proveedores, hacer pedidos y recibirlos; el cajero puede atender clientes y cobrar; el farmacéutico puede validar las recetas que aparezcan en su cola; y el administrador puede entrar a ver los reportes y la bitácora de auditoría.

Lo que queda fuera del MVP a propósito: integraciones con sistemas de facturación electrónica externos, app móvil, ventas en línea para clientes finales, y manejo de sucursales múltiples. La meta es que una farmacia con un solo local pueda reemplazar sus hojas de cálculo desde el primer día, no cubrir todos los casos imaginables.

## Sections

### 1. Autenticación y Roles
Es la puerta de entrada del sistema. Define cómo el personal inicia sesión y qué puede hacer cada quién según su rol; sin esto, ninguna otra sección tiene sentido porque no sabríamos quién está actuando.

### 2. Gestión de Usuarios
Le da al administrador el control de quién forma parte del personal. Permite crear, editar y desactivar cuentas, y asignar el rol que cada persona necesita para hacer su trabajo.

### 3. Inventario
El catálogo de medicamentos con su stock actual, stock mínimo, fecha de vencimiento y precio. Es el corazón operativo: sin saber qué hay y cuánto queda, no se puede vender ni reponer.

### 4. Proveedores y Pedidos
Permite registrar a las empresas que abastecen la farmacia y manejar las órdenes de compra. Cuando el encargado recibe un pedido, el stock se actualiza solo, sin tener que tocar el inventario a mano.

### 5. Clientes
Lleva el registro de las personas que compran, con la opción de marcarlas como frecuentes para descuentos o seguimiento. Sirve para personalizar la atención y para que la venta no sea siempre anónima.

### 6. Ventas (POS)
La pantalla de caja donde el cajero atiende al cliente, arma el ticket, verifica stock al instante y cobra por el medio de pago elegido. Es la sección que más se usa todos los días.

### 7. Recetas
La cola de trabajo del farmacéutico. Cuando una venta incluye un medicamento controlado, la receta queda pendiente hasta que el farmacéutico la apruebe o rechace, dejando observaciones por escrito.

### 8. Reportes y Auditoría
El centro de control del administrador. Reúne reportes de ventas, ganancias, vencimientos y movimientos, y muestra la bitácora completa de acciones sensibles para responder en segundos a la pregunta "¿quién hizo esto y cuándo?".

[Las secciones están ordenadas por prioridad de construcción. Cada una corresponde a un área principal del producto y a una zona de navegación que un usuario abre para hacer su trabajo.]
