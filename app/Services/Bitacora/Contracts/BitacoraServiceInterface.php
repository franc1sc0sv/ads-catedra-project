<?php

declare(strict_types=1);

namespace App\Services\Bitacora\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BitacoraServiceInterface
{
    /**
     * Append-only log of an event to the audit_logs table.
     *
     * @param  array<string, mixed>  $details
     */
    public function log(
        string $action,
        ?int $userId = null,
        ?string $table = null,
        ?string $record = null,
        array $details = []
    ): void;

    /**
     * Filtered, paginated list of audit log entries.
     *
     * Supported keys in $filters:
     *  - user_id (int|string)
     *  - action (string)
     *  - table_affected (string)
     *  - from (Y-m-d)
     *  - to (Y-m-d)
     *
     * Default range when no from/to provided: last 24 hours.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getFiltered(array $filters): LengthAwarePaginator;
}
