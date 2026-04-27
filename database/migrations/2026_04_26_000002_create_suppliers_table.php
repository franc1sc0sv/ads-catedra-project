<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('tax_id')->nullable()->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('company_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
