<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Usuarios\ChangePasswordRequest;
use App\Http\Requests\Usuarios\ResetPasswordRequest;
use App\Models\User;
use App\Services\Usuarios\Contracts\UsuarioServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class PasswordController extends Controller
{
    public function __construct(
        private readonly UsuarioServiceInterface $usuarios,
    ) {}

    public function editSelf(): View
    {
        return view('account.password');
    }

    public function updateSelf(ChangePasswordRequest $request): RedirectResponse
    {
        $user = $request->user();

        $this->usuarios->changePassword(
            $user,
            (string) $request->validated('current_password'),
            (string) $request->validated('password'),
        );

        return redirect()
            ->route('account.password.edit')
            ->with('status', 'Contraseña actualizada correctamente.');
    }

    public function editForUser(User $usuario): View
    {
        return view('admin.usuarios.password', [
            'usuario' => $usuario,
        ]);
    }

    public function resetForUser(ResetPasswordRequest $request, User $usuario): RedirectResponse
    {
        $this->usuarios->resetPasswordByAdmin(
            $usuario,
            (string) $request->validated('password'),
        );

        return redirect()
            ->route('admin.usuarios.index')
            ->with('status', 'Contraseña restablecida para '.$usuario->name.'.');
    }
}
