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
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('system_settings')) {
                $settings = \App\Models\SystemSetting::all();
                
                $apiUrl = $settings->where('key', 'pegawai_api_url')->first();
                if ($apiUrl && $apiUrl->value) {
                    config(['services.pegawai.url' => $apiUrl->value]);
                }

                $apiToken = $settings->where('key', 'pegawai_api_token')->first();
                if ($apiToken && $apiToken->value) {
                    config(['services.pegawai.token' => $apiToken->value]);
                }
            }
        } catch (\Exception $e) {
            // Ignore during migrations or when DB is not ready
        }
    }
}
