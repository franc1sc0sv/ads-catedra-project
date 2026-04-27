<?php

declare(strict_types=1);

namespace App\Services\Usuarios\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UsuarioServiceInterface
{
    /**
     * Listado paginado con búsqueda + filtros.
     *
     * @param  array{search?: ?string, role?: ?string, estado?: ?string}  $filters
     */
    public function list(array $filters): LengthAwarePaginator;

    /**
     * Crea un usuario nuevo. La contraseña se hashea vía cast del modelo.
     *
     * @param  array{name: string, email: string, password: string, role: string}  $data
     */
    public function create(array $data): User;

    /**
     * Actualiza nombre, email y rol. Si el rol cambia, registra ROL_CAMBIADO en bitácora.
     *
     * @param  array{name: string, email: string, role: string}  $data
     */
    public function update(User $user, array $data): User;

    /**
     * Cambio de contraseña por el propio usuario. Re-hashea + cierra sesiones de otros dispositivos.
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): void;

    /**
     * Reset administrativo de contraseña sobre otro usuario. No invoca logoutOtherDevices.
     */
    public function resetPasswordByAdmin(User $target, string $newPassword): void;

    /**
     * Invierte el flag is_active del usuario.
     */
    public function toggleActiva(User $user): void;
}
