<?php

declare(strict_types=1);

use App\Models\Setting;

if (! function_exists('setting')) {
    /**
     * Read a system setting by key with request-scoped memoization.
     *
     * Falls back to $default if the key is missing or the DB is unavailable
     * (so views and controllers never crash on cold caches or migration runs).
     */
    function setting(string $key, mixed $default = null): mixed
    {
        static $cache = [];

        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }

        try {
            $row = Setting::query()->where('key', $key)->first();
            $value = $row?->typedValue();
        } catch (Throwable) {
            $value = null;
        }

        return $cache[$key] = $value ?? $default;
    }
}
