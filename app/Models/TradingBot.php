<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TradingBot extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'version',
        'bot_type',
        'supported_exchanges',
        'supported_pairs',
        'file_path',
        'file_hash',
        'min_balance',
        'default_config',
        'is_active',
        'requires_license',
        'license_key_prefix',
        'installation_guide',
    ];

    protected $casts = [
        'supported_exchanges' => 'array',
        'supported_pairs' => 'array',
        'min_balance' => 'decimal:2',
        'default_config' => 'array',
        'is_active' => 'boolean',
        'requires_license' => 'boolean',
    ];

    /**
     * Get the user bots for this trading bot.
     */
    public function userBots()
    {
        return $this->hasMany(UserBot::class);
    }

    /**
     * Get active user bots for this trading bot.
     */
    public function activeUserBots()
    {
        return $this->hasMany(UserBot::class)->where('status', 'active');
    }

    /**
     * Scope for active bots.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by bot type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('bot_type', $type);
    }

    /**
     * Check if bot supports a specific exchange.
     */
    public function supportsExchange(string $exchange): bool
    {
        return in_array($exchange, $this->supported_exchanges ?? []);
    }

    /**
     * Check if bot supports a specific trading pair.
     */
    public function supportsPair(string $pair): bool
    {
        return in_array($pair, $this->supported_pairs ?? []);
    }

    /**
     * Generate a license key for a user.
     */
    public function generateLicenseKey(): string
    {
        $prefix = $this->license_key_prefix ?? 'BOT';
        $random = strtoupper(bin2hex(random_bytes(8)));
        return $prefix . '-' . $random;
    }

    /**
     * Get the download URL for this bot.
     */
    public function getDownloadUrlAttribute(): string
    {
        return route('bots.download', $this->id);
    }

    /**
     * Get formatted minimum balance.
     */
    public function getFormattedMinBalanceAttribute(): string
    {
        return '$' . number_format($this->min_balance, 2);
    }

    /**
     * Get bot type badge color.
     */
    public function getBotTypeBadgeColorAttribute(): string
    {
        return match($this->bot_type) {
            'scalping' => 'red',
            'swing' => 'blue',
            'arbitrage' => 'green',
            'grid' => 'purple',
            'dca' => 'yellow',
            default => 'gray'
        };
    }

    /**
     * Get total active users count.
     */
    public function getActiveUsersCountAttribute(): int
    {
        return $this->activeUserBots()->count();
    }
}
