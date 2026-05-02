<?php

declare(strict_types=1);

namespace App\Services\Usuarios;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\Bitacora\Contracts\BitacoraServiceInterface;
use App\Services\Usuarios\Contracts\UsuarioServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

final class UsuarioService implements UsuarioServiceInterface
{
    public function __construct(
        private readonly BitacoraServiceInterface $bitacora,
    ) {}

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

        $this->bitacora->log('USUARIO_CREADO', Auth::id(), 'users', (string) $user->id, [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role->value,
        ]);

        return $user;
    }

    public function update(User $user, array $data): User
    {
        $previousRole = $user->role;
        $newRole = UserRole::from($data['role']);
        $previousName = $user->name;
        $previousEmail = $user->email;

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $newRole;
        $user->save();

        if ($previousRole !== $newRole) {
            $this->bitacora->log('ROL_CAMBIADO', Auth::id(), 'users', (string) $user->id, [
                'role_anterior' => $previousRole->value,
                'role_nuevo' => $newRole->value,
            ]);
        }

        if ($previousName !== $user->name || $previousEmail !== $user->email) {
            $this->bitacora->log('USUARIO_EDITADO', Auth::id(), 'users', (string) $user->id, [
                'name' => $user->name,
                'email' => $user->email,
            ]);
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

        $this->bitacora->log('RESET_PASSWORD_ADMIN', Auth::id(), 'users', (string) $target->id, [
            'reset_by' => Auth::id(),
            'target_name' => $target->name,
        ]);
    }

    public function toggleActiva(User $user): void
    {
        if ($user->is_active && $user->role === UserRole::ADMINISTRATOR) {
            throw new \DomainException('No se puede desactivar a un usuario administrador.');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $action = $user->is_active ? 'USUARIO_ACTIVADO' : 'USUARIO_DESACTIVADO';
        $this->bitacora->log($action, Auth::id(), 'users', (string) $user->id, [
            'name' => $user->name,
        ]);
    }
}
