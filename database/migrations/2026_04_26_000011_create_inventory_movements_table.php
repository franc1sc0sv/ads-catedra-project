<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medication_id')->constrained('medications')->restrictOnDelete();
            $table->string('type');
            $table->integer('quantity');
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->string('reason')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('medication_id');
            $table->index('type');
            $table->index('created_at');
            $table->index('user_id');
            $table->index(['medication_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
