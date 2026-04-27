<?php

declare(strict_types=1);

namespace App\Enums;

enum MedicationCategory: string
{
    case OVER_THE_COUNTER = 'over_the_counter';
    case PRESCRIPTION_REQUIRED = 'prescription_required';
    case CONTROLLED = 'controlled';

    public function label(): string
    {
        return match ($this) {
            self::OVER_THE_COUNTER => 'Venta libre',
            self::PRESCRIPTION_REQUIRED => 'Requiere receta',
            self::CONTROLLED => 'Controlado',
        };
    }

    public function requiresPrescription(): bool
    {
        return $this !== self::OVER_THE_COUNTER;
    }
}
