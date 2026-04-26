# Alertas de Stock

## What does this part of the system do?
Es un tablero pensado para que el encargado abra cada mañana y sepa de un vistazo qué necesita atención. Resuelve dos preguntas concretas: ¿de qué se está acabando? y ¿qué está por vencerse?

La idea es que la información llegue antes que el problema. Pedir al proveedor cuando todavía queda margen es mejor que pedirlo cuando ya no hay; sacar un producto del estante antes de que venza es mejor que descubrirlo vencido frente a un cliente.

El tablero no solo muestra el problema, también ofrece el siguiente paso: desde un ítem de stock bajo el encargado puede saltar a crear un pedido al proveedor; desde uno por vencer, puede saltar a registrar la baja correspondiente.

## Who uses it?
El encargado de inventario lo usa como herramienta diaria; el administrador lo consulta en modo lectura para tener visibilidad del estado del inventario.

## How does it work?
El tablero se divide en dos bloques. El primero lista todos los medicamentos cuyo stock actual quedó por debajo del mínimo configurado en el catálogo, ordenados por urgencia (los más críticos arriba); cada fila muestra el medicamento, su stock actual, su mínimo y un acceso directo para crear un pedido al proveedor asociado. El segundo bloque lista los medicamentos cuya fecha de vencimiento entra dentro de la ventana próxima; cada fila muestra el medicamento, su fecha de vencimiento y los días restantes, y ofrece un acceso directo para registrar la baja por vencimiento si el producto ya pasó su fecha. Si un mismo medicamento aparece en los dos bloques (poco stock y por vencer), simplemente aparece en ambos: son problemas distintos. El tablero refleja el estado actual cada vez que se carga; no se actualiza solo en tiempo real, se refresca al recargar la página.

La ventana de días para el bloque de "próximos a vencer" no está cableada en código: se lee de la tabla de configuración del sistema, en la clave `dias_alerta_vencimiento`, con default de 30 días. El admin puede ajustar ese valor desde la pantalla de configuración global y el cambio aplica al instante en el tablero la próxima vez que el encargado lo recarga; si el negocio quiere mirar 60 días de anticipación porque el proveedor tarda en reponer, basta con cambiar esa clave. La misma lógica aplica para el umbral de aviso de stock bajo: se lee de la configuración global (`dias_aviso_stock_bajo`), de modo que ajustar la sensibilidad del tablero es trabajo de un admin, no de un desarrollador.

## Skills relevantes

- `/laravel-patterns` — para optimizar las queries de stock bajo y vencimientos
- `/tailwind-css-patterns` — para el dashboard con cards y enlaces a las acciones siguientes
