<?php

declare(strict_types=1);

namespace App\Services\Bitacora;

use App\Models\AuditLog;
use App\Services\Bitacora\Contracts\BitacoraServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class BitacoraService implements BitacoraServiceInterface
{
    public function log(
        string $action,
        ?int $userId = null,
        ?string $table = null,
        ?string $record = null,
        array $details = []
    ): void {
        $ip = null;

        if (app()->bound('request')) {
            $ip = request()->ip();
        }

        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'table_affected' => $table,
            'record_affected' => $record,
            'details' => $details === [] ? null : json_encode($details, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'ip_address' => $ip,
        ]);
    }

    public function getFiltered(array $filters): LengthAwarePaginator
    {
        $query = AuditLog::query()->with('user');

        $userId = $filters['user_id'] ?? null;
        if ($userId !== null && $userId !== '') {
            $query->where('user_id', (int) $userId);
        }

        $action = trim((string) ($filters['action'] ?? ''));
        if ($action !== '') {
            $driver = $query->getModel()->getConnection()->getDriverName();
            $op = $driver === 'pgsql' ? 'ilike' : 'like';
            $query->where('action', $op, '%'.$action.'%');
        }

        $table = trim((string) ($filters['table_affected'] ?? ''));
        if ($table !== '') {
            $driver = $query->getModel()->getConnection()->getDriverName();
            $op = $driver === 'pgsql' ? 'ilike' : 'like';
            $query->where('table_affected', $op, '%'.$table.'%');
        }

        $from = trim((string) ($filters['from'] ?? ''));
        $to = trim((string) ($filters['to'] ?? ''));

        if ($from === '' && $to === '') {
            $query->where('created_at', '>=', now()->subDay());
        } else {
            if ($from !== '') {
                $query->where('created_at', '>=', $from.' 00:00:00');
            }
            if ($to !== '') {
                $query->where('created_at', '<=', $to.' 23:59:59');
            }
        }

        return $query->orderByDesc('created_at')->paginate(25)->withQueryString();
    }
}
