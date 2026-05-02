<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Reportes;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

final class ReportesHubController extends Controller
{
    public function index(): View
    {
        return view('admin.reportes.index');
    }

    public function inventoryManager(): View
    {
        return view('inventory-manager.reportes.index');
    }
}
