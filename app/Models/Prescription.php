<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PrescriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prescription extends Model
{
    use HasFactory;

    public $timestamps = true;

    const UPDATED_AT = null;

    protected $fillable = [
        'prescription_number',
        'patient_name',
        'doctor_name',
        'doctor_license',
        'issued_at',
        'expires_at',
        'status',
        'pharmacist_id',
        'validated_at',
        'notes',
        'medication_id',
        'current_reviewer_id',
        'lock_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'expires_at' => 'date',
            'status' => PrescriptionStatus::class,
            'validated_at' => 'datetime',
            'lock_expires_at' => 'datetime',
        ];
    }

    public function pharmacist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pharmacist_id');
    }

    public function currentReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_reviewer_id');
    }

    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }

    public function salePrescriptions(): HasMany
    {
        return $this->hasMany(SalePrescription::class);
    }

    public function isLocked(): bool
    {
        return $this->current_reviewer_id !== null
            && $this->lock_expires_at !== null
            && $this->lock_expires_at->isFuture();
    }
}
