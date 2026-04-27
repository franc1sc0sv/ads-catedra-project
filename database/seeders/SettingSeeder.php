<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\SettingType;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            [
                'key' => 'dias_alerta_vencimiento',
                'value' => '30',
                'description' => 'Días antes del vencimiento para mostrar alertas de stock próximo a vencer.',
                'data_type' => SettingType::INTEGER->value,
                'editable' => true,
            ],
            [
                'key' => 'tasa_iva',
                'value' => '0.13',
                'description' => 'Tasa de IVA aplicada al subtotal de cada venta.',
                'data_type' => SettingType::DECIMAL->value,
                'editable' => true,
            ],
            [
                'key' => 'nombre_farmacia',
                'value' => 'FarmaSys Demo',
                'description' => 'Nombre comercial de la farmacia (aparece en tickets y reportes).',
                'data_type' => SettingType::STRING->value,
                'editable' => true,
            ],
            [
                'key' => 'direccion_farmacia',
                'value' => 'San Salvador, El Salvador',
                'description' => 'Dirección visible en tickets y reportes.',
                'data_type' => SettingType::STRING->value,
                'editable' => true,
            ],
            [
                'key' => 'lock_receta_minutos',
                'value' => '5',
                'description' => 'TTL en minutos del lock pesimista de revisión de recetas.',
                'data_type' => SettingType::INTEGER->value,
                'editable' => true,
            ],
            [
                'key' => 'dias_validez_receta',
                'value' => '30',
                'description' => 'Días de vigencia por defecto al registrar una receta.',
                'data_type' => SettingType::INTEGER->value,
                'editable' => true,
            ],
            [
                'key' => 'permite_venta_sin_cliente',
                'value' => '1',
                'description' => 'Permitir registrar ventas rápidas sin asociar cliente.',
                'data_type' => SettingType::BOOLEAN->value,
                'editable' => true,
            ],
        ];

        foreach ($defaults as $row) {
            Setting::updateOrCreate(['key' => $row['key']], $row);
        }
    }
}
