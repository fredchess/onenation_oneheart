<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $primaryKey = 'key';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['key', 'value'];

    protected $casts = ['value' => 'json'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::find($key);

        return $setting !== null ? $setting->value : $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
