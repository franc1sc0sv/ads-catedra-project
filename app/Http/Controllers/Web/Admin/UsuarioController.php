<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Usuarios\CreateUsuarioRequest;
use App\Http\Requests\Usuarios\UpdateUsuarioRequest;
use App\Models\User;
use App\Services\Usuarios\Contracts\UsuarioServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class UsuarioController extends Controller
{
    public function __construct(
        private readonly UsuarioServiceInterface $usuarios,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', 'string'],
            'estado' => ['nullable', 'string', 'in:activos,inactivos,todos'],
        ]);

        $usuarios = $this->usuarios->list($filters);

        return view('admin.usuarios.index', [
            'usuarios' => $usuarios,
            'filters' => $filters,
            'roles' => UserRole::cases(),
        ]);
    }

    public function create(): View
    {
        return view('admin.usuarios.create', [
            'roles' => UserRole::cases(),
        ]);
    }

    public function store(CreateUsuarioRequest $request): RedirectResponse
    {
        $this->usuarios->create($request->validated());

        return redirect()
            ->route('admin.usuarios.index')
            ->with('status', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario): View
    {
        return view('admin.usuarios.edit', [
            'usuario' => $usuario,
            'roles' => UserRole::cases(),
        ]);
    }

    public function update(UpdateUsuarioRequest $request, User $usuario): RedirectResponse
    {
        $this->usuarios->update($usuario, $request->validated());

        return redirect()
            ->route('admin.usuarios.index')
            ->with('status', 'Usuario actualizado correctamente.');
    }

    public function toggleActiva(User $usuario): RedirectResponse
    {
        try {
            $this->usuarios->toggleActiva($usuario);
        } catch (\DomainException $e) {
            return redirect()
                ->route('admin.usuarios.index')
                ->with('error', $e->getMessage());
        }

        $estado = $usuario->is_active ? 'activada' : 'desactivada';

        return redirect()
            ->route('admin.usuarios.index')
            ->with('status', "Cuenta {$estado} correctamente.");
    }
}
