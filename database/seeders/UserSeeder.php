<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['admin@pharma.test',     'Admin User',     'administrator',     'Admin User'],
            ['admin2@pharma.test',    'Admin Backup',   'administrator',     'Admin Backup'],
            ['sales@pharma.test',     'Sales User',     'salesperson',       'Sales User'],
            ['inventory@pharma.test', 'Inventory User', 'inventory_manager', 'Inventory User'],
            ['pharmacist@pharma.test', 'Pharmacist User', 'pharmacist',        'Pharmacist User'],
        ];

        foreach ($accounts as [$email, $name, $role]) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'role' => $role,
                    'is_active' => true,
                ],
            );
        }
    }
}
