<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentMethod: string
{
    case CASH = 'cash';
    case CREDIT_CARD = 'credit_card';
    case DEBIT_CARD = 'debit_card';
    case TRANSFER = 'transfer';

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Efectivo',
            self::CREDIT_CARD => 'Tarjeta de crédito',
            self::DEBIT_CARD => 'Tarjeta de débito',
            self::TRANSFER => 'Transferencia',
        };
    }
}
