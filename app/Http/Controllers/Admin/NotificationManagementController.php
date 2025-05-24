<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AdminNotification;
use App\Notifications\SystemAlert;

class NotificationManagementController extends Controller
{
    public function index()
    {
        $stats = [
            'total_sent' => UserNotification::count(),
            'unread_count' => UserNotification::where('read_at', null)->count(),
            'today_sent' => UserNotification::whereDate('created_at', today())->count(),
            'email_sent' => UserNotification::where('type', 'email')->count(),
        ];

        $recentNotifications = UserNotification::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.notifications.index', compact('stats', 'recentNotifications'));
    }

    public function templates()
    {
        $templates = [
            'welcome' => [
                'name' => 'Welcome Email',
                'description' => 'Sent to new users after registration',
                'type' => 'email',
                'active' => true,
            ],
            'subscription_activated' => [
                'name' => 'Subscription Activated',
                'description' => 'Sent when user subscription is activated',
                'type' => 'email',
                'active' => true,
            ],
            'subscription_expired' => [
                'name' => 'Subscription Expired',
                'description' => 'Sent when user subscription expires',
                'type' => 'email',
                'active' => true,
            ],
            'payment_received' => [
                'name' => 'Payment Received',
                'description' => 'Sent when payment is successfully processed',
                'type' => 'email',
                'active' => true,
            ],
            'expert_signal_new' => [
                'name' => 'New Expert Signal',
                'description' => 'Sent when new expert signal is published',
                'type' => 'both',
                'active' => true,
            ],
            'bot_error' => [
                'name' => 'Bot Error Alert',
                'description' => 'Sent when trading bot encounters an error',
                'type' => 'both',
                'active' => true,
            ],
            'system_maintenance' => [
                'name' => 'System Maintenance',
                'description' => 'Sent before scheduled maintenance',
                'type' => 'both',
                'active' => true,
            ],
        ];

        return view('admin.notifications.templates', compact('templates'));
    }

    public function editTemplate($template)
    {
        $templateData = $this->getTemplateData($template);
        
        if (!$templateData) {
            abort(404);
        }

        return view('admin.notifications.edit-template', compact('template', 'templateData'));
    }

    public function updateTemplate(Request $request, $template)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'email_content' => 'required|string',
            'push_content' => 'nullable|string',
            'active' => 'boolean',
        ]);

        // Save template data (you might want to store this in database or config)
        $templatePath = resource_path("views/emails/templates/{$template}.blade.php");
        
        if (!file_exists(dirname($templatePath))) {
            mkdir(dirname($templatePath), 0755, true);
        }

        file_put_contents($templatePath, $request->email_content);

        return redirect()->route('admin.notifications.templates')
            ->with('success', 'Template updated successfully.');
    }

    public function send()
    {
        $users = User::where('email_verified_at', '!=', null)->get();
        
        return view('admin.notifications.send', compact('users'));
    }

    public function sendNotification(Request $request)
    {
        $request->validate([
            'type' => 'required|in:email,push,both',
            'recipients' => 'required|in:all,active_subscribers,specific',
            'specific_users' => 'required_if:recipients,specific|array',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'send_immediately' => 'boolean',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $users = $this->getRecipients($request->recipients, $request->specific_users);

        if ($request->boolean('send_immediately')) {
            $this->sendImmediateNotification($users, $request);
        } else {
            $this->scheduleNotification($users, $request);
        }

        return redirect()->back()->with('success', 'Notification sent successfully.');
    }

    public function history(Request $request)
    {
        $query = UserNotification::with('user');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('message', 'like', '%' . $request->search . '%');
            });
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.notifications.history', compact('notifications'));
    }

    public function systemAlerts()
    {
        $alerts = [
            'payment_failures' => [
                'name' => 'Payment Failures',
                'description' => 'Alert when payment failure rate exceeds threshold',
                'threshold' => 10,
                'enabled' => true,
            ],
            'high_error_rate' => [
                'name' => 'High Error Rate',
                'description' => 'Alert when system error rate is high',
                'threshold' => 5,
                'enabled' => true,
            ],
            'low_disk_space' => [
                'name' => 'Low Disk Space',
                'description' => 'Alert when disk space is running low',
                'threshold' => 85,
                'enabled' => true,
            ],
            'suspicious_activity' => [
                'name' => 'Suspicious Activity',
                'description' => 'Alert for potential security threats',
                'threshold' => 1,
                'enabled' => true,
            ],
        ];

        return view('admin.notifications.system-alerts', compact('alerts'));
    }

    public function updateSystemAlerts(Request $request)
    {
        $request->validate([
            'alerts' => 'required|array',
            'alerts.*.enabled' => 'boolean',
            'alerts.*.threshold' => 'required|numeric|min:0',
        ]);

        // Save alert settings (implement based on your storage preference)
        foreach ($request->alerts as $alertKey => $alertData) {
            // Save to database or config file
        }

        return redirect()->back()->with('success', 'System alerts updated successfully.');
    }

    public function preferences()
    {
        $preferences = [
            'email_notifications' => true,
            'push_notifications' => true,
            'sms_notifications' => false,
            'notification_frequency' => 'immediate',
            'quiet_hours_enabled' => false,
            'quiet_hours_start' => '22:00',
            'quiet_hours_end' => '08:00',
        ];

        return view('admin.notifications.preferences', compact('preferences'));
    }

    public function updatePreferences(Request $request)
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'notification_frequency' => 'required|in:immediate,hourly,daily',
            'quiet_hours_enabled' => 'boolean',
            'quiet_hours_start' => 'required_if:quiet_hours_enabled,true|date_format:H:i',
            'quiet_hours_end' => 'required_if:quiet_hours_enabled,true|date_format:H:i',
        ]);

        // Save preferences
        return redirect()->back()->with('success', 'Notification preferences updated successfully.');
    }

    private function getRecipients($type, $specificUsers = null)
    {
        switch ($type) {
            case 'all':
                return User::where('email_verified_at', '!=', null)->get();
            
            case 'active_subscribers':
                return User::whereHas('subscription', function ($query) {
                    $query->where('status', 'active');
                })->get();
            
            case 'specific':
                return User::whereIn('id', $specificUsers ?? [])->get();
            
            default:
                return collect();
        }
    }

    private function sendImmediateNotification($users, $request)
    {
        foreach ($users as $user) {
            if (in_array($request->type, ['email', 'both'])) {
                $user->notify(new AdminNotification(
                    $request->subject,
                    $request->message,
                    'email'
                ));
            }

            if (in_array($request->type, ['push', 'both'])) {
                UserNotification::create([
                    'user_id' => $user->id,
                    'type' => 'push',
                    'title' => $request->subject,
                    'message' => $request->message,
                    'data' => json_encode(['admin_sent' => true]),
                ]);
            }
        }
    }

    private function scheduleNotification($users, $request)
    {
        // Implement notification scheduling logic
        // You might want to use Laravel's job queue for this
    }

    private function getTemplateData($template)
    {
        $templates = [
            'welcome' => [
                'subject' => 'Welcome to Gemini Pro Trader',
                'email_content' => 'Welcome email content...',
                'push_content' => 'Welcome to Gemini Pro Trader!',
                'active' => true,
            ],
            // Add other templates...
        ];

        return $templates[$template] ?? null;
    }
}