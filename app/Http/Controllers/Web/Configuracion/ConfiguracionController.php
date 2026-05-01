<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Configuracion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Configuracion\UpdateConfiguracionRequest;
use App\Services\Configuracion\Contracts\ConfiguracionServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use InvalidArgumentException;

final class ConfiguracionController extends Controller
{
    public function __construct(
        private readonly ConfiguracionServiceInterface $service,
    ) {}

    public function index(): View
    {
        return view('admin.configuracion.index', [
            'configs' => $this->service->allEditable(),
        ]);
    }

    public function update(UpdateConfiguracionRequest $request, string $key): RedirectResponse
    {
        try {
            $this->service->update($key, (string) $request->validated('value'));
        } catch (InvalidArgumentException $e) {
            return redirect()
                ->route('admin.configuracion.index')
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.configuracion.index')
            ->with('success', 'Configuración actualizada correctamente.');
    }
}
