<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentMethod: string
{
    case CASH = 'cash';

    public function label(): string
    {
        return 'Efectivo';
    }
}
