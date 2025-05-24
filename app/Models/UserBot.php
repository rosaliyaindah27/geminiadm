<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserBot extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trading_bot_id',
        'license_key',
        'status',
        'activated_at',
        'expires_at',
        'bot_config',
        'exchange_connected',
        'performance_stats',
        'last_activity',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
        'bot_config' => 'array',
        'performance_stats' => 'array',
        'last_activity' => 'datetime',
    ];

    /**
     * Get the user that owns this bot.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the trading bot.
     */
    public function tradingBot()
    {
        return $this->belongsTo(TradingBot::class);
    }

    /**
     * Scope for active user bots.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for expired user bots.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    /**
     * Check if bot is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               (!$this->expires_at || $this->expires_at > now());
    }

    /**
     * Check if bot is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at < now();
    }

    /**
     * Activate the bot.
     */
    public function activate(): void
    {
        $this->update([
            'status' => 'active',
            'activated_at' => now(),
        ]);
    }

    /**
     * Deactivate the bot.
     */
    public function deactivate(): void
    {
        $this->update([
            'status' => 'inactive',
        ]);
    }

    /**
     * Suspend the bot.
     */
    public function suspend(): void
    {
        $this->update([
            'status' => 'suspended',
        ]);
    }

    /**
     * Update last activity.
     */
    public function updateActivity(): void
    {
        $this->update([
            'last_activity' => now(),
        ]);
    }

    /**
     * Get days remaining until expiration.
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->expires_at) {
            return null;
        }

        if ($this->isExpired()) {
            return 0;
        }

        return $this->expires_at->diffInDays(now());
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'green',
            'inactive' => 'gray',
            'expired' => 'red',
            'suspended' => 'yellow',
            default => 'gray'
        };
    }

    /**
     * Get performance summary.
     */
    public function getPerformanceSummaryAttribute(): array
    {
        $stats = $this->performance_stats ?? [];
        
        return [
            'total_trades' => $stats['total_trades'] ?? 0,
            'profitable_trades' => $stats['profitable_trades'] ?? 0,
            'total_profit' => $stats['total_profit'] ?? 0,
            'win_rate' => $stats['total_trades'] > 0 ? 
                round(($stats['profitable_trades'] ?? 0) / $stats['total_trades'] * 100, 2) : 0,
        ];
    }
}
