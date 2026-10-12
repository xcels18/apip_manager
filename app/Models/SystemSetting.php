<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    public static function valueFor(string $key, mixed $default = null): mixed
    {
        return app(\App\Services\SettingsService::class)->all()[$key] ?? $default;
    }

    protected static function booted(): void
    {
        static::saved(fn () => app(\App\Services\SettingsService::class)->forget());
        static::deleted(fn () => app(\App\Services\SettingsService::class)->forget());
    }

    protected $fillable = ['key', 'value'];
}
