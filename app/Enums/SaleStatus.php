<?php

declare(strict_types=1);

namespace App\Enums;

enum SaleStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendiente',
            self::COMPLETED => 'Completada',
            self::CANCELLED => 'Anulada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'amber',
            self::COMPLETED => 'emerald',
            self::CANCELLED => 'rose',
        };
    }
}