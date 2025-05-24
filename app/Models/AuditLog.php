<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'level',
        'type',
    ];

    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function model()
    {
        return $this->morphTo();
    }

    public static function log($action, $description = null, $model = null, $oldValues = null, $newValues = null, $level = 'info', $type = 'admin')
    {
        $user = auth()->user();
        
        return static::create([
            'user_id' => $user ? $user->id : null,
            'action' => $action,
            'description' => $description,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'level' => $level,
            'type' => $type,
        ]);
    }

    public static function logUserAction($action, $description = null, $model = null)
    {
        return static::log($action, $description, $model, null, null, 'info', 'user');
    }

    public static function logSystemEvent($action, $description = null, $level = 'info')
    {
        return static::log($action, $description, null, null, null, $level, 'system');
    }

    public static function logSecurityEvent($action, $description = null, $level = 'warning')
    {
        return static::log($action, $description, null, null, null, $level, 'security');
    }

    public static function logModelChange($action, $model, $oldValues = null, $newValues = null)
    {
        $description = ucfirst($action) . ' ' . class_basename($model);
        return static::log($action, $description, $model, $oldValues, $newValues, 'info', 'admin');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function getFormattedChangesAttribute()
    {
        if (!$this->old_values || !$this->new_values) {
            return null;
        }

        $changes = [];
        
        foreach ($this->new_values as $key => $newValue) {
            $oldValue = $this->old_values[$key] ?? null;
            
            if ($oldValue !== $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }
}