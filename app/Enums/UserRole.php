<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case ADMINISTRATOR = 'administrator';
    case SALESPERSON = 'salesperson';
    case INVENTORY_MANAGER = 'inventory_manager';
    case PHARMACIST = 'pharmacist';

    public function label(): string
    {
        return match ($this) {
            self::ADMINISTRATOR => 'Administrator',
            self::SALESPERSON => 'Salesperson',
            self::INVENTORY_MANAGER => 'Inventory Manager',
            self::PHARMACIST => 'Pharmacist',
        };
    }
}
