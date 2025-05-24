<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExpertSignal extends Model
{
    use HasFactory;

    protected $fillable = [
        'pair',
        'signal_type',
        'entry_price',
        'take_profit',
        'stop_loss',
        'analysis_reason',
        'status',
        'created_by',
        'approved_by',
        'published_at',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'entry_price' => 'decimal:8',
        'take_profit' => 'decimal:8',
        'stop_loss' => 'decimal:8',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the user who created this signal.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved this signal.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope for published signals.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for pending signals.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for active signals (published and not expired).
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'published')
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    /**
     * Check if signal is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at < now();
    }

    /**
     * Approve the signal.
     */
    public function approve(User $approver): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
        ]);
    }

    /**
     * Publish the signal.
     */
    public function publish(): void
    {
        $this->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    /**
     * Reject the signal.
     */
    public function reject(): void
    {
        $this->update([
            'status' => 'rejected',
        ]);
    }

    /**
     * Get formatted entry price.
     */
    public function getFormattedEntryPriceAttribute(): string
    {
        return number_format($this->entry_price, 8);
    }

    /**
     * Get signal type badge color.
     */
    public function getSignalTypeBadgeColorAttribute(): string
    {
        return match($this->signal_type) {
            'BUY' => 'green',
            'SELL' => 'red',
            'HODL' => 'blue',
            default => 'gray'
        };
    }
}
