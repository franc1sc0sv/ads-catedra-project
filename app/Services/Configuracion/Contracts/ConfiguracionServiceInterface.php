<?php

declare(strict_types=1);

namespace App\Services\Configuracion\Contracts;

use App\Models\Setting;
use Illuminate\Support\Collection;

interface ConfiguracionServiceInterface
{
    /**
     * Return the typed value for the given key.
     *
     * @throws \RuntimeException When the key is not present in the settings table.
     */
    public function getValue(string $key): mixed;

    /**
     * Update an editable setting after validating the raw value against its data type.
     *
     * @throws \InvalidArgumentException When the setting is not editable or the value is invalid for its type.
     * @throws \RuntimeException When the key does not exist.
     */
    public function update(string $key, string $rawValue): Setting;

    /**
     * Return all settings ordered by key. The view distinguishes editable vs read-only rows.
     */
    public function allEditable(): Collection;
}
