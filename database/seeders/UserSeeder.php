<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->administrator()->create([
            'name'  => 'Admin User',
            'email' => 'admin@pharma.test',
        ]);

        User::factory()->salesperson()->create([
            'name'  => 'Sales User',
            'email' => 'sales@pharma.test',
        ]);

        User::factory()->inventoryManager()->create([
            'name'  => 'Inventory User',
            'email' => 'inventory@pharma.test',
        ]);

        User::factory()->pharmacist()->create([
            'name'  => 'Pharmacist User',
            'email' => 'pharmacist@pharma.test',
        ]);
    }
}
