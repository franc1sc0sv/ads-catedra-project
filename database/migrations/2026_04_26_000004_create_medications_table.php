<?php

declare(strict_types=1);

use App\Enums\MedicationCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('barcode')->nullable()->unique();
            $table->decimal('price', 12, 2);
            $table->integer('stock')->default(0);
            $table->integer('min_stock')->default(10);
            $table->date('expires_at');
            $table->string('category')->default(MedicationCategory::OVER_THE_COUNTER->value);
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('name');
            $table->index('expires_at');
            $table->index('category');
            $table->index(['supplier_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
