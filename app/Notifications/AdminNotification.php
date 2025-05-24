<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $subject;
    protected $message;
    protected $type;
    protected $data;

    public function __construct($subject, $message, $type = 'email', $data = [])
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->type = $type;
        $this->data = $data;
    }

    public function via($notifiable)
    {
        $channels = [];
        
        if (in_array($this->type, ['email', 'both'])) {
            $channels[] = 'mail';
        }
        
        if (in_array($this->type, ['database', 'push', 'both'])) {
            $channels[] = 'database';
        }
        
        return $channels;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->subject)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($this->message)
            ->line('This is an important notification from the Gemini Pro Trader admin team.')
            ->action('Visit Dashboard', url('/dashboard'))
            ->line('Thank you for using Gemini Pro Trader!');
    }

    public function toArray($notifiable)
    {
        return [
            'subject' => $this->subject,
            'message' => $this->message,
            'type' => $this->type,
            'data' => $this->data,
            'admin_sent' => true,
        ];
    }
}