<?php

declare(strict_types=1);

namespace App\Services\Usuarios;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\Usuarios\Contracts\UsuarioServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

final class UsuarioService implements UsuarioServiceInterface
{
    public function list(array $filters): LengthAwarePaginator
    {
        $query = User::query()->orderBy('name');

        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $like = '%'.$search.'%';
            $driver = $query->getModel()->getConnection()->getDriverName();
            $op = $driver === 'pgsql' ? 'ilike' : 'like';

            $query->where(function ($q) use ($like, $op) {
                $q->where('name', $op, $like)
                    ->orWhere('email', $op, $like);
            });
        }

        $role = $filters['role'] ?? null;
        if ($role !== null && $role !== '' && $role !== 'todos') {
            $query->where('role', $role);
        }

        $estado = $filters['estado'] ?? null;
        if ($estado === 'activos') {
            $query->where('is_active', true);
        } elseif ($estado === 'inactivos') {
            $query->where('is_active', false);
        }

        return $query->paginate(15)->withQueryString();
    }

    public function create(array $data): User
    {
        $user = new User;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->role = UserRole::from($data['role']);
        $user->is_active = true;
        $user->save();

        return $user;
    }

    public function update(User $user, array $data): User
    {
        $previousRole = $user->role;
        $newRole = UserRole::from($data['role']);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $newRole;
        $user->save();

        if ($previousRole !== $newRole) {
            // TODO(bitacora): cuando exista App\Services\Bitacora\Contracts\BitacoraServiceInterface,
            // inyectar y llamar log('ROL_CAMBIADO', auth()->id(), 'users', (string) $user->id, [
            //     'role_anterior' => $previousRole->value,
            //     'role_nuevo'    => $newRole->value,
            // ]).
        }

        return $user;
    }

    public function changePassword(User $user, string $currentPassword, string $newPassword): void
    {
        if (! Hash::check($currentPassword, $user->password)) {
            // El FormRequest ya valida current_password; este check es defensa en profundidad.
            return;
        }

        $user->password = $newPassword;
        $user->save();

        Auth::logoutOtherDevices($newPassword);
    }

    public function resetPasswordByAdmin(User $target, string $newPassword): void
    {
        $target->password = $newPassword;
        $target->save();

        // TODO(bitacora): cuando exista BitacoraServiceInterface, registrar
        // log('reset_password_admin', auth()->id(), 'users', (string) $target->id, []).
    }

    public function toggleActiva(User $user): void
    {
        if ($user->is_active && $user->role === UserRole::ADMINISTRATOR) {
            throw new \DomainException('No se puede desactivar a un usuario administrador.');
        }

        $user->is_active = ! $user->is_active;
        $user->save();
    }
}
