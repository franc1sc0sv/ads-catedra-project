# Catálogo de Clientes

## What does this part of the system do?
Es el lugar central donde viven todos los clientes de la farmacia. Aquí se ven todos en una tabla, se buscan por nombre o por identificación (DUI) y se mantienen sus datos al día.

Desde esta pantalla se da de alta un cliente nuevo capturando nombre, teléfono, correo, dirección, identificación y la bandera de frecuente. También se editan los datos cuando cambian (un cambio de teléfono, una corrección al nombre) y se desactivan los clientes que ya no son relevantes.

La idea es tener una sola fuente de verdad: si un cajero o un administrador necesita encontrar a alguien o actualizar sus datos, lo hace desde aquí sin tener que pasar por otros módulos.

## Who uses it?
Cajeros y administradores, ambos con permisos para listar, crear, editar y desactivar clientes.

## How does it work?
Al entrar se ve la tabla con los clientes activos por defecto y un cuadro de búsqueda arriba que filtra por nombre o por identificación a medida que se escribe. Si se busca a alguien que no aparece, hay un botón para crear uno nuevo que abre el formulario completo con todos los campos. La identificación tiene que ser única en todo el sistema, así que si se intenta registrar un DUI que ya existe, el formulario lo marca como error y no deja guardar — esto evita duplicados que después ensucian el historial. La edición funciona igual: se puede cambiar cualquier dato menos la identificación si ya tiene ventas asociadas, para no romper la trazabilidad. Desactivar un cliente es un soft-delete; el cliente desaparece de la búsqueda durante la venta pero su ficha y su historial siguen consultables desde una vista de "incluir desactivados", y se puede reactivar más tarde si vuelve a ser relevante.

## Skills relevantes

- `/laravel-specialist` — para el CRUD con la regla de unicidad sobre identificación y el soft-delete del cliente.
- `/tailwind-css-patterns` — para la tabla con búsqueda en vivo y los formularios de alta y edición.
