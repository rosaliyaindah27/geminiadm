<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'api_key',
        'api_enabled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'api_enabled' => 'boolean',
        ];
    }

    /**
     * Get the user's subscriptions.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the user's active subscription.
     */
    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active');
    }

    /**
     * Get the user's trading bots.
     */
    public function userBots()
    {
        return $this->hasMany(UserBot::class);
    }

    /**
     * Get the user's payments.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the user's notifications.
     */
    public function userNotifications()
    {
        return $this->hasMany(UserNotification::class);
    }

    /**
     * Get expert signals created by this user.
     */
    public function createdSignals()
    {
        return $this->hasMany(ExpertSignal::class, 'created_by');
    }

    /**
     * Get expert signals approved by this user.
     */
    public function approvedSignals()
    {
        return $this->hasMany(ExpertSignal::class, 'approved_by');
    }

    /**
     * Check if user has an active subscription.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->is_admin || $this->hasRole('admin');
    }

    /**
     * Check if user is signal provider.
     */
    public function isSignalProvider(): bool
    {
        return $this->hasRole('signal_provider');
    }
}
