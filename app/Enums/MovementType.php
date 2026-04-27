<?php

declare(strict_types=1);

namespace App\Enums;

enum MovementType: string
{
    case PURCHASE_IN = 'purchase_in';
    case SALE_OUT = 'sale_out';
    case MANUAL_ADJUST = 'manual_adjust';
    case CUSTOMER_RETURN = 'customer_return';
    case EXPIRY_WRITEOFF = 'expiry_writeoff';

    public function label(): string
    {
        return match ($this) {
            self::PURCHASE_IN => 'Entrada por compra',
            self::SALE_OUT => 'Salida por venta',
            self::MANUAL_ADJUST => 'Ajuste manual',
            self::CUSTOMER_RETURN => 'Devolución',
            self::EXPIRY_WRITEOFF => 'Baja por vencimiento',
        };
    }

    public function requiresReason(): bool
    {
        return match ($this) {
            self::MANUAL_ADJUST, self::CUSTOMER_RETURN, self::EXPIRY_WRITEOFF => true,
            self::PURCHASE_IN, self::SALE_OUT => false,
        };
    }

    public function increasesStock(): bool
    {
        return match ($this) {
            self::PURCHASE_IN, self::CUSTOMER_RETURN => true,
            self::SALE_OUT, self::EXPIRY_WRITEOFF => false,
            self::MANUAL_ADJUST => false,
        };
    }
}
