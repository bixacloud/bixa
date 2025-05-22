<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Setting extends Model
{
    protected $fillable = ['key', 'value'];
    protected $casts = [
        'value' => 'json',
    ];
    
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }
    
    public static function set($key, $value)
    {
        $setting = static::firstOrNew(['key' => $key]);
        $setting->value = $value;
        $setting->save();
        return $setting;
    }
}