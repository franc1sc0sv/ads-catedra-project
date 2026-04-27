<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('prescription_id')->constrained('prescriptions')->restrictOnDelete();
            $table->foreignId('medication_id')->constrained('medications')->restrictOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['sale_id', 'medication_id']);
            $table->index('sale_id');
            $table->index('prescription_id');
            $table->index('medication_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_prescriptions');
    }
};
