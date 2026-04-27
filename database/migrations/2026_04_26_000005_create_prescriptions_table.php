<?php

declare(strict_types=1);

use App\Enums\PrescriptionStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->string('prescription_number')->unique();
            $table->string('patient_name');
            $table->string('doctor_name');
            $table->string('doctor_license');
            $table->date('issued_at');
            $table->date('expires_at');
            $table->string('status')->default(PrescriptionStatus::PENDING->value);
            $table->foreignId('pharmacist_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->string('notes')->nullable();
            $table->foreignId('medication_id')->constrained('medications')->restrictOnDelete();
            $table->foreignId('current_reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('lock_expires_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('status');
            $table->index('pharmacist_id');
            $table->index('medication_id');
            $table->index('current_reviewer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
