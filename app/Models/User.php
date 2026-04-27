<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active', 'last_login_at'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'salesperson_id');
    }

    public function requestedPurchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'requested_by_id');
    }

    public function receivedPurchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'received_by_id');
    }

    public function validatedPrescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class, 'pharmacist_id');
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }
}
