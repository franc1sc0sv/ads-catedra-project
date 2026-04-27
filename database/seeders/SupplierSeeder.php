<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'company_name' => 'Distribuidora Farmacéutica Central',
                'phone' => '+503 2222-1100',
                'email' => 'ventas@dfcentral.test',
                'address' => 'Bvld. Los Próceres, San Salvador',
                'tax_id' => '0614-150189-101-0',
            ],
            [
                'company_name' => 'Laboratorios Andinos S.A.',
                'phone' => '+503 2233-4400',
                'email' => 'pedidos@andinos.test',
                'address' => 'Zona Industrial Soyapango',
                'tax_id' => '0614-220290-102-1',
            ],
            [
                'company_name' => 'MediImport CA',
                'phone' => '+503 2244-5500',
                'email' => 'compras@mediimport.test',
                'address' => 'Santa Tecla, La Libertad',
                'tax_id' => '0614-300391-103-2',
            ],
        ];

        foreach ($suppliers as $row) {
            Supplier::updateOrCreate(['tax_id' => $row['tax_id']], $row);
        }
    }
}
