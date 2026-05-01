<?php

declare(strict_types=1);

namespace App\Services\Configuracion;

use App\Enums\SettingType;
use App\Models\Setting;
use App\Services\Configuracion\Contracts\ConfiguracionServiceInterface;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;

final class ConfiguracionService implements ConfiguracionServiceInterface
{
    public function getValue(string $key): mixed
    {
        $setting = Setting::query()->where('key', $key)->first();

        if ($setting === null) {
            throw new RuntimeException("La configuración '{$key}' no existe.");
        }

        return $setting->data_type->cast($setting->value);
    }

    public function update(string $key, string $rawValue): Setting
    {
        $setting = Setting::query()->where('key', $key)->first();

        if ($setting === null) {
            throw new RuntimeException("La configuración '{$key}' no existe.");
        }

        if ($setting->editable !== true) {
            throw new InvalidArgumentException("La configuración '{$key}' no es editable.");
        }

        $normalized = $this->validateAndNormalize($setting->data_type, $rawValue);

        $setting->value = $normalized;
        $setting->save();

        return $setting;
    }

    public function allEditable(): Collection
    {
        return Setting::query()->where('editable', true)->orderBy('key')->get();
    }

    private function validateAndNormalize(SettingType $type, string $rawValue): string
    {
        return match ($type) {
            SettingType::INTEGER => $this->normalizeInteger($rawValue),
            SettingType::DECIMAL => $this->normalizeDecimal($rawValue),
            SettingType::BOOLEAN => $this->normalizeBoolean($rawValue),
            SettingType::STRING => $rawValue,
        };
    }

    private function normalizeInteger(string $raw): string
    {
        if (! is_numeric($raw) || (string) (int) $raw !== ltrim($raw, '+')) {
            // Accept numeric strings that round-trip as integers (no decimals).
            if (! preg_match('/^-?\d+$/', $raw)) {
                throw new InvalidArgumentException('El valor debe ser un número entero.');
            }
        }

        return (string) (int) $raw;
    }

    private function normalizeDecimal(string $raw): string
    {
        if (! is_numeric($raw)) {
            throw new InvalidArgumentException('El valor debe ser un número decimal.');
        }

        return (string) (float) $raw;
    }

    private function normalizeBoolean(string $raw): string
    {
        $truthy = ['1', 'true', 'on'];
        $falsy = ['0', 'false', 'off'];

        $needle = strtolower($raw);

        if (in_array($needle, $truthy, true)) {
            return '1';
        }

        if (in_array($needle, $falsy, true)) {
            return '0';
        }

        throw new InvalidArgumentException('El valor booleano debe ser 0/1, true/false u on/off.');
    }
}
