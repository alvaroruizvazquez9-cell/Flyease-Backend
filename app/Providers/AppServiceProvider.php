<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Asegurar que las rutas de cache existan
    $paths = [
        storage_path('framework/views'),
        storage_path('framework/cache'),
        storage_path('framework/sessions'),
    ];

    foreach ($paths as $path) {
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }
    }
}
