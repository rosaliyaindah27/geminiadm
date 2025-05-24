<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemAlert extends Notification implements ShouldQueue
{
    use Queueable;

    protected $alertType;
    protected $message;
    protected $severity;
    protected $data;

    public function __construct($alertType, $message, $severity = 'medium', $data = [])
    {
        $this->alertType = $alertType;
        $this->message = $message;
        $this->severity = $severity;
        $this->data = $data;
    }

    public function via($notifiable)
    {
        $channels = ['database'];
        
        // Send email for high severity alerts
        if ($this->severity === 'high' || $this->severity === 'critical') {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    public function toMail($notifiable)
    {
        $subject = 'System Alert: ' . ucfirst($this->alertType);
        
        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->greeting('System Alert!')
            ->line('A system alert has been triggered:')
            ->line('**Alert Type:** ' . ucfirst($this->alertType))
            ->line('**Severity:** ' . ucfirst($this->severity))
            ->line('**Message:** ' . $this->message);

        if (!empty($this->data)) {
            $mailMessage->line('**Additional Details:**');
            foreach ($this->data as $key => $value) {
                $mailMessage->line('- ' . ucfirst($key) . ': ' . $value);
            }
        }

        $mailMessage->action('View Admin Panel', url('/admin'))
                   ->line('Please investigate this alert as soon as possible.');

        return $mailMessage;
    }

    public function toArray($notifiable)
    {
        return [
            'alert_type' => $this->alertType,
            'message' => $this->message,
            'severity' => $this->severity,
            'data' => $this->data,
            'timestamp' => now()->toISOString(),
        ];
    }
}