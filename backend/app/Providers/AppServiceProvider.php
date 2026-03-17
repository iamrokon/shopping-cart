<?php

namespace App\Providers;

use Illuminate\Auth\AuthenticationException;
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
        // swagger-php v6 calls trigger_error() with E_USER_WARNING for non-critical issues.
        // Laravel converts E_USER_WARNING to ErrorException via its error handler.
        // We silence swagger-related warnings to prevent false crashes during doc generation.
        $originalHandler = set_error_handler(function (int $errno, string $errstr, string $errfile = '', int $errline = 0): bool {
            // If this warning comes from swagger-php, silently ignore it
            if (str_contains($errfile, 'swagger-php') || str_contains($errfile, 'l5-swagger')) {
                return true; // handled — do not propagate to PHP's error handler
            }

            // Re-raise for all other errors so Laravel handles them normally
            return false;
        });
    }
}
