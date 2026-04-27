<?php

declare(strict_types=1);

namespace App\Enums;

enum PrescriptionStatus: string
{
    case PENDING = 'pending';
    case VALIDATED = 'validated';
    case REJECTED = 'rejected';
    case VOIDED = 'voided';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendiente',
            self::VALIDATED => 'Validada',
            self::REJECTED => 'Rechazada',
            self::VOIDED => 'Anulada',
        };
    }

    public function isFinal(): bool
    {
        return match ($this) {
            self::VALIDATED, self::REJECTED, self::VOIDED => true,
            self::PENDING => false,
        };
    }
}
