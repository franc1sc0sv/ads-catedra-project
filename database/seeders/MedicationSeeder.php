<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\MedicationCategory;
use App\Models\Medication;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class MedicationSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = Supplier::all()->keyBy('company_name');

        if ($suppliers->isEmpty()) {
            return;
        }

        $central = $suppliers->get('Distribuidora Farmacéutica Central');
        $andinos = $suppliers->get('Laboratorios Andinos S.A.');
        $medi = $suppliers->get('MediImport CA');

        $defaultSupplier = $suppliers->first();

        $medications = [
            ['Acetaminofén 500mg',     'Analgésico y antipirético, caja x 20.',          '7501001100001', 1.50,  120, 20, '+1 year',  MedicationCategory::OVER_THE_COUNTER,      $central],
            ['Ibuprofeno 400mg',       'Antiinflamatorio no esteroideo, caja x 10.',     '7501001100002', 2.10,  85,  20, '+18 months', MedicationCategory::OVER_THE_COUNTER,    $central],
            ['Loratadina 10mg',        'Antihistamínico, caja x 10.',                    '7501001100003', 3.40,  60,  15, '+2 years',   MedicationCategory::OVER_THE_COUNTER,    $andinos],
            ['Suero oral electrolítico', 'Sobre 1 L.',                                    '7501001100004', 0.80,  200, 30, '+9 months',  MedicationCategory::OVER_THE_COUNTER,    $medi],
            ['Vitamina C 1g',          'Tabletas efervescentes, tubo x 10.',             '7501001100005', 4.50,  40,  15, '+1 year',    MedicationCategory::OVER_THE_COUNTER,    $andinos],

            ['Amoxicilina 500mg',      'Antibiótico betalactámico, caja x 21.',          '7501001100010', 6.80,  50,  10, '+1 year',    MedicationCategory::PRESCRIPTION_REQUIRED, $central],
            ['Azitromicina 500mg',     'Antibiótico macrólido, caja x 3.',               '7501001100011', 8.20,  35,  10, '+1 year',    MedicationCategory::PRESCRIPTION_REQUIRED, $andinos],
            ['Metformina 850mg',       'Antidiabético oral, caja x 30.',                 '7501001100012', 5.10,  70,  15, '+2 years',   MedicationCategory::PRESCRIPTION_REQUIRED, $medi],
            ['Losartán 50mg',          'Antihipertensivo, caja x 30.',                   '7501001100013', 5.60,  65,  15, '+2 years',   MedicationCategory::PRESCRIPTION_REQUIRED, $medi],

            ['Diazepam 10mg',          'Ansiolítico controlado (lista IV), caja x 30.',  '7501001100020', 7.40,  25,  10, '+1 year',    MedicationCategory::CONTROLLED,            $andinos],
            ['Tramadol 50mg',          'Analgésico opioide controlado, caja x 20.',      '7501001100021', 9.10,  20,  10, '+1 year',    MedicationCategory::CONTROLLED,            $central],
            ['Morfina 10mg/ml amp.',   'Opioide controlado, ampolla 1 ml.',              '7501001100022', 12.50, 8,   5,  '+10 months', MedicationCategory::CONTROLLED,            $medi],
        ];

        foreach ($medications as [$name, $description, $barcode, $price, $stock, $minStock, $expiresIn, $category, $supplier]) {
            Medication::updateOrCreate(
                ['barcode' => $barcode],
                [
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'stock' => $stock,
                    'min_stock' => $minStock,
                    'expires_at' => now()->modify($expiresIn)->format('Y-m-d'),
                    'category' => $category->value,
                    'supplier_id' => ($supplier ?? $defaultSupplier)->id,
                    'is_active' => true,
                ],
            );
        }
    }
}
