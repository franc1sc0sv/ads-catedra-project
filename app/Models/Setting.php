<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SettingType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'description',
        'data_type',
        'editable',
    ];

    protected function casts(): array
    {
        return [
            'data_type' => SettingType::class,
            'editable' => 'boolean',
        ];
    }

    public function typedValue(): int|string|bool|float
    {
        return $this->data_type->cast($this->value);
    }
}
