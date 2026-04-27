<?php

declare(strict_types=1);

namespace App\Enums;

enum SettingType: string
{
    case INTEGER = 'integer';
    case STRING = 'string';
    case BOOLEAN = 'boolean';
    case DECIMAL = 'decimal';

    public function label(): string
    {
        return match ($this) {
            self::INTEGER => 'Entero',
            self::STRING => 'Texto',
            self::BOOLEAN => 'Booleano',
            self::DECIMAL => 'Decimal',
        };
    }

    public function cast(string $raw): int|string|bool|float
    {
        return match ($this) {
            self::INTEGER => (int) $raw,
            self::STRING => $raw,
            self::BOOLEAN => filter_var($raw, FILTER_VALIDATE_BOOLEAN),
            self::DECIMAL => (float) $raw,
        };
    }
}
