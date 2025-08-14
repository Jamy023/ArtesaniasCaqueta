<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        if (config('app.env') === 'production' || request()->header('x-forwarded-proto') === 'https') {
            URL::forceScheme('https');
        }
        
        // Crear enlace storage en producción si no existe
        if (app()->environment('production')) {
            $link = public_path('storage');
            $target = storage_path('app/public');
            
            if (!file_exists($link)) {
                if (PHP_OS_FAMILY === 'Windows') {
                    exec("mklink /D \"$link\" \"$target\"");
                } else {
                    symlink($target, $link);
                }
            }
        }
    }
}
