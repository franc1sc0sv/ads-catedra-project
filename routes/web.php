<?php

declare(strict_types=1);

// Phase A — auth + dashboards. Each phase B domain owns its own file.

require __DIR__.'/web/auth.php';
require __DIR__.'/web/dashboards.php';

require __DIR__.'/web/usuarios.php';
require __DIR__.'/web/inventario.php';
require __DIR__.'/web/proveedores.php';
require __DIR__.'/web/ventas.php';
require __DIR__.'/web/recetas.php';
require __DIR__.'/web/reportes.php';
require __DIR__.'/web/configuracion.php';
require __DIR__.'/web/clientes.php';
