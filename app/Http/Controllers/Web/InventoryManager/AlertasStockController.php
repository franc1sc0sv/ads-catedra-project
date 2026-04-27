<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\InventoryManager;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Services\Inventario\Contracts\AlertasStockServiceInterface;
use Illuminate\View\View;

final class AlertasStockController extends Controller
{
    public function __construct(
        private readonly AlertasStockServiceInterface $alertas,
    ) {}

    public function index(): View
    {
        $bajoMinimo = $this->alertas->getBajoMinimo();
        $proximosVencer = $this->alertas->getProximosVencer();

        $view = match (auth()->user()->role) {
            UserRole::ADMINISTRATOR => 'admin.inventario.alertas',
            default => 'inventario.alertas',
        };

        return view($view, [
            'bajoMinimo' => $bajoMinimo,
            'proximosVencer' => $proximosVencer,
        ]);
    }
}
