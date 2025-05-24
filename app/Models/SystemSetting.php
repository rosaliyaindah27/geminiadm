<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'group',
    ];

    protected $casts = [
        // Removed 'value' => 'json' to avoid conflict with custom accessor/mutator
    ];

    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        return $setting->value;
    }

    public static function set($key, $value, $type = 'string', $description = null, $group = 'general')
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'description' => $description,
                'group' => $group,
            ]
        );
    }

    public static function getGroup($group)
    {
        return static::where('group', $group)->get()->pluck('value', 'key');
    }

    public static function getAllSettings()
    {
        return static::all()->groupBy('group');
    }

    public function getValueAttribute($value)
    {
        if ($this->type === 'boolean') {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        if ($this->type === 'integer') {
            return (int) $value;
        }

        if ($this->type === 'float') {
            return (float) $value;
        }

        if ($this->type === 'array' || $this->type === 'json') {
            return json_decode($value, true);
        }

        return $value;
    }

    public function setValueAttribute($value)
    {
        if ($this->type === 'array' || $this->type === 'json') {
            $this->attributes['value'] = json_encode($value);
        } else {
            $this->attributes['value'] = $value;
        }
    }
}