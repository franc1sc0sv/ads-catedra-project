<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('sales')
            ->where('status', 'in_progress')
            ->update(['status' => 'pending']);
    }

    public function down(): void
    {
        // Irreversible: 'in_progress' is no longer a valid SaleStatus case.
    }
};
