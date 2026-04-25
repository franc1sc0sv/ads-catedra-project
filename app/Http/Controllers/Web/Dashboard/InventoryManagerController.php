<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class InventoryManagerController extends Controller
{
    public function index(): View
    {
        return view('inventory-manager.dashboard.index');
    }
}
