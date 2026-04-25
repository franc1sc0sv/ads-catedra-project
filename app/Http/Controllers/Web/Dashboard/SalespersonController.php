<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SalespersonController extends Controller
{
    public function index(): View
    {
        return view('salesperson.dashboard.index');
    }
}
