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
        $this->app->scoped(\App\Services\PegawaiService::class);
        $this->app->scoped(\App\Services\SettingsService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\RateLimiter::for('login', function ($request) {
            return [
                \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by(strtolower((string) $request->input('email')).'|'.$request->ip()),
                \Illuminate\Cache\RateLimiting\Limit::perMinute(30)->by($request->ip()),
            ];
        });
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('system_settings')) {
                $settings = app(\App\Services\SettingsService::class)->all();

                $apiUrl = $settings['pegawai_api_url'] ?? null;
                if ($apiUrl) {
                    config(['services.pegawai.url' => $apiUrl]);
                }

                $apiToken = $settings['pegawai_api_token'] ?? null;
                if ($apiToken) {
                    config(['services.pegawai.token' => $apiToken]);
                }
            }
        } catch (\Exception $e) {
            // Ignore during migrations or when DB is not ready
        }
    }
}
