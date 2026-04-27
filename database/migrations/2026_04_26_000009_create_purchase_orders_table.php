<?php

declare(strict_types=1);

use App\Enums\PurchaseOrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            $table->foreignId('requested_by_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('received_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('ordered_at')->useCurrent();
            $table->timestamp('received_at')->nullable();
            $table->string('status')->default(PurchaseOrderStatus::REQUESTED->value);
            $table->decimal('total_estimated', 14, 2)->default(0);
            $table->string('notes')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->timestamps();

            $table->index('supplier_id');
            $table->index('status');
            $table->index('ordered_at');
            $table->index('requested_by_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
