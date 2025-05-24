<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\ExpertSignal;
use App\Models\TradingBot;
use App\Models\UserBot;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{


    public function index()
    {
        // Key metrics
        $totalUsers = User::count();
        $activeSubscriptions = Subscription::active()->count();
        $totalRevenue = Payment::where('payment_status', 'finished')->sum('price_amount');
        $pendingSignals = ExpertSignal::where('status', 'pending')->count();

        // Recent registrations (last 30 days)
        $recentRegistrations = User::where('created_at', '>=', Carbon::now()->subDays(30))->count();
        
        // Monthly revenue trend (last 6 months)
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = Payment::where('payment_status', 'finished')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('price_amount');
            $monthlyRevenue[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue
            ];
        }

        // Subscription distribution
        $subscriptionStats = Subscription::active()
            ->join('subscription_plans', 'subscriptions.subscription_plan_id', '=', 'subscription_plans.id')
            ->selectRaw('subscription_plans.name, COUNT(*) as count')
            ->groupBy('subscription_plans.name')
            ->get();

        // Recent activities
        $recentUsers = User::latest()->limit(5)->get();
        $recentPayments = Payment::with(['user', 'subscription.subscriptionPlan'])
            ->latest()
            ->limit(5)
            ->get();
        $recentSignals = ExpertSignal::with('creator')
            ->latest()
            ->limit(5)
            ->get();

        // Bot usage stats
        $activeBots = UserBot::active()->count();
        $totalBotDownloads = UserBot::count();

        // System health indicators
        $systemHealth = [
            'database' => $this->checkDatabaseHealth(),
            'storage' => $this->checkStorageHealth(),
            'cache' => $this->checkCacheHealth(),
        ];

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeSubscriptions',
            'totalRevenue',
            'pendingSignals',
            'recentRegistrations',
            'monthlyRevenue',
            'subscriptionStats',
            'recentUsers',
            'recentPayments',
            'recentSignals',
            'activeBots',
            'totalBotDownloads',
            'systemHealth'
        ));
    }

    public function analytics()
    {
        // User growth analytics
        $userGrowth = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = User::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $userGrowth[] = [
                'month' => $month->format('M Y'),
                'users' => $count
            ];
        }

        // Revenue analytics
        $revenueAnalytics = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = Payment::where('payment_status', 'finished')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('price_amount');
            $revenueAnalytics[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue
            ];
        }

        // Subscription churn rate
        $churnRate = $this->calculateChurnRate();

        // Top performing signals
        $topSignals = ExpertSignal::published()
            ->withCount('views')
            ->orderBy('views_count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.analytics', compact(
            'userGrowth',
            'revenueAnalytics',
            'churnRate',
            'topSignals'
        ));
    }

    private function checkDatabaseHealth(): string
    {
        try {
            \DB::connection()->getPdo();
            return 'healthy';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    private function checkStorageHealth(): string
    {
        try {
            $freeSpace = disk_free_space(storage_path());
            $totalSpace = disk_total_space(storage_path());
            $usagePercent = (($totalSpace - $freeSpace) / $totalSpace) * 100;
            
            if ($usagePercent > 90) {
                return 'warning';
            } elseif ($usagePercent > 95) {
                return 'error';
            }
            return 'healthy';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    private function checkCacheHealth(): string
    {
        try {
            \Cache::put('health_check', 'ok', 60);
            $value = \Cache::get('health_check');
            return $value === 'ok' ? 'healthy' : 'error';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    private function calculateChurnRate(): float
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $activeAtStart = Subscription::where('status', 'active')
            ->where('created_at', '<', $startOfMonth)
            ->count();
            
        $cancelledThisMonth = Subscription::where('status', 'cancelled')
            ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
            ->count();
            
        return $activeAtStart > 0 ? ($cancelledThisMonth / $activeAtStart) * 100 : 0;
    }
}
