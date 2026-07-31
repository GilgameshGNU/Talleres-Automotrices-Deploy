<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

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
        // Rate Limiter: Login
        RateLimiter::for('login-attempts', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'message'     => 'Demasiados intentos de inicio de sesión. Intenta de nuevo en 1 minuto.',
                        'retry_after' => 60,
                    ], 429);
                });
        });

        // Rate Limiter: API General
        RateLimiter::for('api-general', function (Request $request) {
            return Limit::perMinute(120)
                ->by($request->user()?->idUsuario ?: $request->ip());
        });

        // Rate Limiter: Escrituras
        RateLimiter::for('api-escritura', function (Request $request) {
            if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                return Limit::perMinute(30)
                    ->by($request->user()?->idUsuario ?: $request->ip());
            }
            return Limit::none();
        });

        // Rate Limiter: Descargas
        RateLimiter::for('descargas', function (Request $request) {
            return Limit::perMinute(10)
                ->by($request->user()?->idUsuario ?: $request->ip());
        });
    }
}
