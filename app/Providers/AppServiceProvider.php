<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Auth\AuthService;
use App\Services\Auth\Contracts\AuthServiceInterface;
use App\Services\Inventario\AlertasStockService;
use App\Services\Inventario\Contracts\AlertasStockServiceInterface;
use App\Services\Inventario\Contracts\MedicamentoServiceInterface;
use App\Services\Inventario\Contracts\MovimientoServiceInterface;
use App\Services\Inventario\Contracts\StockServiceInterface;
use App\Services\Inventario\MedicamentoService;
use App\Services\Inventario\MovimientoService;
use App\Services\Inventario\StockService;
use App\Services\Proveedores\Contracts\PedidoServiceInterface;
use App\Services\Proveedores\Contracts\ProveedorServiceInterface;
use App\Services\Proveedores\PedidoService;
use App\Services\Proveedores\ProveedorService;
use App\Services\Usuarios\Contracts\UsuarioServiceInterface;
use App\Services\Usuarios\UsuarioService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuthServiceInterface::class, AuthService::class);

        $this->app->bind(UsuarioServiceInterface::class, UsuarioService::class);

        $this->app->bind(MedicamentoServiceInterface::class, MedicamentoService::class);
        $this->app->bind(AlertasStockServiceInterface::class, AlertasStockService::class);
        $this->app->bind(StockServiceInterface::class, StockService::class);
        $this->app->bind(MovimientoServiceInterface::class, MovimientoService::class);

        $this->app->bind(ProveedorServiceInterface::class, ProveedorService::class);
        $this->app->bind(PedidoServiceInterface::class, PedidoService::class);
    }

    public function boot(): void
    {
        //
    }
}
