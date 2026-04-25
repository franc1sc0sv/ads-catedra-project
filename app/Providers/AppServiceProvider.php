<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Services\Jwt\JwtService::class);

        $this->app->bind(
            \App\Services\Auth\Contracts\AuthServiceInterface::class,
            \App\Services\Auth\AuthService::class
        );
    }

    public function boot(): void
    {
        //
    }
}
