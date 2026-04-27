<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name' => 'María Elena González',
                'phone' => '+503 7000-1111',
                'email' => 'maria.gonzalez@test.local',
                'address' => 'Col. Escalón, San Salvador',
                'identification' => '01234567-8',
                'is_frequent' => true,
            ],
            [
                'name' => 'Carlos Alberto Rivas',
                'phone' => '+503 7000-2222',
                'email' => 'carlos.rivas@test.local',
                'address' => 'Santa Tecla',
                'identification' => '02345678-9',
                'is_frequent' => true,
            ],
            [
                'name' => 'Ana Sofía Martínez',
                'phone' => '+503 7000-3333',
                'email' => null,
                'address' => 'Soyapango',
                'identification' => '03456789-0',
                'is_frequent' => false,
            ],
            [
                'name' => 'José Luis Hernández',
                'phone' => '+503 7000-4444',
                'email' => 'jose.hernandez@test.local',
                'address' => 'Mejicanos',
                'identification' => '04567890-1',
                'is_frequent' => false,
            ],
            [
                'name' => 'Cliente General',
                'phone' => null,
                'email' => null,
                'address' => null,
                'identification' => null,
                'is_frequent' => false,
            ],
        ];

        foreach ($customers as $row) {
            $key = $row['identification']
                ? ['identification' => $row['identification']]
                : ['name' => $row['name']];

            Customer::updateOrCreate($key, $row);
        }
    }
}
