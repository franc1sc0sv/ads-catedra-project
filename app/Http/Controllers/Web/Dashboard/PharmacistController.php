<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

final class PharmacistController extends Controller
{
    public function index(): View
    {
        return view('pharmacist.dashboard.index');
    }
}
