<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'nowpayments_payment_id',
        'nowpayments_order_id',
        'payment_status',
        'pay_amount',
        'pay_currency',
        'price_amount',
        'price_currency',
        'pay_address',
        'actually_paid',
        'outcome_amount',
        'outcome_currency',
        'payment_extra',
        'description',
        'payment_created_at',
        'payment_updated_at',
    ];

    protected $casts = [
        'pay_amount' => 'decimal:8',
        'price_amount' => 'decimal:2',
        'actually_paid' => 'decimal:8',
        'payment_extra' => 'array',
        'payment_created_at' => 'datetime',
        'payment_updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns this payment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subscription associated with this payment.
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Scope for successful payments.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('payment_status', 'finished');
    }

    /**
     * Scope for pending payments.
     */
    public function scopePending($query)
    {
        return $query->whereIn('payment_status', ['waiting', 'confirming']);
    }

    /**
     * Scope for failed payments.
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('payment_status', ['failed', 'expired']);
    }

    /**
     * Check if payment is successful.
     */
    public function isSuccessful(): bool
    {
        return $this->payment_status === 'finished';
    }

    /**
     * Check if payment is pending.
     */
    public function isPending(): bool
    {
        return in_array($this->payment_status, ['waiting', 'confirming']);
    }

    /**
     * Check if payment is failed.
     */
    public function isFailed(): bool
    {
        return in_array($this->payment_status, ['failed', 'expired']);
    }

    /**
     * Get formatted pay amount.
     */
    public function getFormattedPayAmountAttribute(): string
    {
        return number_format($this->pay_amount, 8) . ' ' . $this->pay_currency;
    }

    /**
     * Get formatted price amount.
     */
    public function getFormattedPriceAmountAttribute(): string
    {
        return $this->price_currency . ' ' . number_format($this->price_amount, 2);
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->payment_status) {
            'finished' => 'green',
            'waiting', 'confirming' => 'yellow',
            'failed', 'expired' => 'red',
            'partially_paid' => 'orange',
            default => 'gray'
        };
    }

    /**
     * Get human readable status.
     */
    public function getHumanStatusAttribute(): string
    {
        return match($this->payment_status) {
            'waiting' => 'Waiting for Payment',
            'confirming' => 'Confirming Payment',
            'confirmed' => 'Payment Confirmed',
            'sending' => 'Processing',
            'partially_paid' => 'Partially Paid',
            'finished' => 'Completed',
            'failed' => 'Failed',
            'refunded' => 'Refunded',
            'expired' => 'Expired',
            default => ucfirst($this->payment_status)
        };
    }
}
