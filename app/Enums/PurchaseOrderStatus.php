<?php

declare(strict_types=1);

namespace App\Enums;

enum PurchaseOrderStatus: string
{
    case REQUESTED = 'requested';
    case SHIPPED = 'shipped';
    case RECEIVED = 'received';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::REQUESTED => 'Solicitado',
            self::SHIPPED => 'Enviado',
            self::RECEIVED => 'Recibido',
            self::CANCELLED => 'Cancelado',
        };
    }
}
