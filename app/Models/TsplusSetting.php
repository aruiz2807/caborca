<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class TsplusSetting extends Model
{
    use HasFactory;

    protected $table = 'rdp_settings';

    protected $fillable = [
        'url',
    ];

    public static function current(): ?self
    {
        if (! Schema::hasTable('rdp_settings')) {
            return null;
        }

        return Cache::remember('tsplus.setting.current', 60, fn () => static::query()->first());
    }

    public static function configuredUrl(): ?string
    {
        return static::current()?->url;
    }

    public static function forgetCurrent(): void
    {
        Cache::forget('tsplus.setting.current');
    }
}
