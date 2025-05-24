<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpertSignal;
use App\Models\UserBot;
use App\Models\UserNotification;
use App\Models\Payment;

class DashboardController extends Controller
{

    public function index()
    {
        $user = auth()->user();
        
        // Get user's active subscription
        $activeSubscription = $user->activeSubscription;
        
        // Get recent expert signals
        $recentSignals = ExpertSignal::active()
            ->latest('published_at')
            ->limit(5)
            ->get();
        
        // Get user's active bots
        $activeBots = $user->userBots()
            ->active()
            ->with('tradingBot')
            ->get();
        
        // Get unread notifications
        $unreadNotifications = $user->userNotifications()
            ->unread()
            ->latest()
            ->limit(5)
            ->get();
        
        // Get recent payments
        $recentPayments = $user->payments()
            ->latest()
            ->limit(3)
            ->get();
        
        // Calculate stats
        $stats = [
            'active_bots' => $activeBots->count(),
            'total_signals' => $recentSignals->count(),
            'unread_notifications' => $unreadNotifications->count(),
            'subscription_status' => $activeSubscription ? 'active' : 'inactive',
            'days_remaining' => $activeSubscription ? $activeSubscription->days_remaining : 0,
        ];
        
        return view('dashboard', compact(
            'user',
            'activeSubscription',
            'recentSignals',
            'activeBots',
            'unreadNotifications',
            'recentPayments',
            'stats'
        ));
    }
}
