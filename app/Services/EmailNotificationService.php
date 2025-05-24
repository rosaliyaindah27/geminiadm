<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserNotification;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailNotificationService
{
    public function sendWelcomeEmail(User $user)
    {
        try {
            Mail::to($user->email)->send(new WelcomeEmail($user));

            $this->logNotification($user, 'welcome_email', 'Welcome email sent successfully');
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email: ' . $e->getMessage());
            return false;
        }
    }

    public function sendSubscriptionActivatedEmail(User $user, $subscription)
    {
        try {
            Mail::send('emails.subscription-activated', [
                'user' => $user,
                'subscription' => $subscription
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Your Subscription is Now Active!');
            });

            $this->logNotification($user, 'subscription_activated', 'Subscription activated email sent');
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send subscription activated email: ' . $e->getMessage());
            return false;
        }
    }

    public function sendSubscriptionExpiredEmail(User $user, $subscription)
    {
        try {
            Mail::send('emails.subscription-expired', [
                'user' => $user,
                'subscription' => $subscription
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Your Subscription Has Expired');
            });

            $this->logNotification($user, 'subscription_expired', 'Subscription expired email sent');
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send subscription expired email: ' . $e->getMessage());
            return false;
        }
    }

    public function sendPaymentReceivedEmail(User $user, $payment)
    {
        try {
            Mail::send('emails.payment-received', [
                'user' => $user,
                'payment' => $payment
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Payment Received - Thank You!');
            });

            $this->logNotification($user, 'payment_received', 'Payment received email sent');
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send payment received email: ' . $e->getMessage());
            return false;
        }
    }

    public function sendExpertSignalEmail(User $user, $signal)
    {
        try {
            Mail::send('emails.expert-signal', [
                'user' => $user,
                'signal' => $signal
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('New Expert Signal Available!');
            });

            $this->logNotification($user, 'expert_signal', 'Expert signal email sent');
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send expert signal email: ' . $e->getMessage());
            return false;
        }
    }

    public function sendBotErrorEmail(User $user, $bot, $error)
    {
        try {
            Mail::send('emails.bot-error', [
                'user' => $user,
                'bot' => $bot,
                'error' => $error
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Trading Bot Error Alert');
            });

            $this->logNotification($user, 'bot_error', 'Bot error email sent');
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send bot error email: ' . $e->getMessage());
            return false;
        }
    }

    public function sendSystemMaintenanceEmail(User $user, $maintenanceInfo)
    {
        try {
            Mail::send('emails.system-maintenance', [
                'user' => $user,
                'maintenance' => $maintenanceInfo
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Scheduled System Maintenance Notice');
            });

            $this->logNotification($user, 'system_maintenance', 'System maintenance email sent');
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send system maintenance email: ' . $e->getMessage());
            return false;
        }
    }

    public function sendPasswordResetEmail(User $user, $token)
    {
        try {
            Mail::send('emails.password-reset', [
                'user' => $user,
                'token' => $token,
                'url' => url('/password/reset/' . $token . '?email=' . urlencode($user->email))
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Reset Your Password');
            });

            $this->logNotification($user, 'password_reset', 'Password reset email sent');
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send password reset email: ' . $e->getMessage());
            return false;
        }
    }

    public function sendEmailVerificationEmail(User $user)
    {
        try {
            $verificationUrl = url('/email/verify/' . $user->id . '/' . sha1($user->email));
            
            Mail::send('emails.email-verification', [
                'user' => $user,
                'verificationUrl' => $verificationUrl
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Verify Your Email Address');
            });

            $this->logNotification($user, 'email_verification', 'Email verification sent');
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email verification: ' . $e->getMessage());
            return false;
        }
    }

    public function sendBulkEmail($users, $subject, $template, $data = [])
    {
        $successCount = 0;
        $failureCount = 0;

        foreach ($users as $user) {
            try {
                Mail::send($template, array_merge($data, ['user' => $user]), function ($message) use ($user, $subject) {
                    $message->to($user->email, $user->name)
                            ->subject($subject);
                });

                $this->logNotification($user, 'bulk_email', 'Bulk email sent: ' . $subject);
                $successCount++;
                
            } catch (\Exception $e) {
                Log::error('Failed to send bulk email to ' . $user->email . ': ' . $e->getMessage());
                $failureCount++;
            }
        }

        return [
            'success' => $successCount,
            'failed' => $failureCount,
            'total' => count($users)
        ];
    }

    private function logNotification(User $user, $type, $message)
    {
        UserNotification::create([
            'user_id' => $user->id,
            'type' => 'email',
            'title' => ucfirst(str_replace('_', ' ', $type)),
            'message' => $message,
            'data' => json_encode(['email_type' => $type]),
        ]);
    }

    public function testEmailConfiguration()
    {
        try {
            Mail::raw('This is a test email to verify email configuration.', function ($message) {
                $message->to(config('mail.from.address'))
                        ->subject('Email Configuration Test');
            });
            
            return true;
        } catch (\Exception $e) {
            Log::error('Email configuration test failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getEmailStats()
    {
        return [
            'total_sent' => UserNotification::where('type', 'email')->count(),
            'sent_today' => UserNotification::where('type', 'email')
                ->whereDate('created_at', today())
                ->count(),
            'sent_this_week' => UserNotification::where('type', 'email')
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'sent_this_month' => UserNotification::where('type', 'email')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }
}