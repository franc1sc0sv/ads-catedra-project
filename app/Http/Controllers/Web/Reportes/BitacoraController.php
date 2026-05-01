<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Reportes;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Bitacora\Contracts\BitacoraServiceInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class BitacoraController extends Controller
{
    public function __construct(
        private readonly BitacoraServiceInterface $bitacora,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'user_id' => ['nullable', 'integer'],
            'action' => ['nullable', 'string', 'max:100'],
            'table_affected' => ['nullable', 'string', 'max:100'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $entries = $this->bitacora->getFiltered($filters);

        return view('admin.reportes.bitacora', [
            'entries' => $entries,
            'filters' => $filters,
            'usuarios' => User::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
