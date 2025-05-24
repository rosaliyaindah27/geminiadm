<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialManagementController extends Controller
{
    public function index()
    {
        $stats = [
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'monthly_revenue' => Payment::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
        ];

        return view('admin.financial.index', compact('stats'));
    }

    public function transactions(Request $request)
    {
        $query = Payment::with(['user', 'subscription.plan']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.financial.transactions', compact('transactions'));
    }

    public function subscriptions(Request $request)
    {
        $query = Subscription::with(['user', 'plan']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('plan_id')) {
            $query->where('subscription_plan_id', $request->plan_id);
        }

        $subscriptions = $query->orderBy('created_at', 'desc')->paginate(20);
        $plans = SubscriptionPlan::all();

        return view('admin.financial.subscriptions', compact('subscriptions', 'plans'));
    }

    public function plans()
    {
        $plans = SubscriptionPlan::withCount('subscriptions')->get();
        return view('admin.financial.plans', compact('plans'));
    }

    public function createPlan()
    {
        return view('admin.financial.create-plan');
    }

    public function storePlan(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'features' => 'required|array',
            'is_active' => 'boolean',
        ]);

        SubscriptionPlan::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'billing_cycle' => $request->billing_cycle,
            'features' => $request->features,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.financial.plans')
            ->with('success', 'Subscription plan created successfully.');
    }

    public function editPlan(SubscriptionPlan $plan)
    {
        return view('admin.financial.edit-plan', compact('plan'));
    }

    public function updatePlan(Request $request, SubscriptionPlan $plan)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'features' => 'required|array',
            'is_active' => 'boolean',
        ]);

        $plan->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'billing_cycle' => $request->billing_cycle,
            'features' => $request->features,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.financial.plans')
            ->with('success', 'Subscription plan updated successfully.');
    }

    public function revenueReport(Request $request)
    {
        $period = $request->get('period', 'monthly');
        
        $data = [];
        
        if ($period === 'monthly') {
            $data = Payment::where('status', 'completed')
                ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as total')
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->limit(12)
                ->get();
        } else {
            $data = Payment::where('status', 'completed')
                ->selectRaw('YEAR(created_at) as year, SUM(amount) as total')
                ->groupBy('year')
                ->orderBy('year', 'desc')
                ->limit(5)
                ->get();
        }

        return view('admin.financial.revenue-report', compact('data', 'period'));
    }

    public function refundPayment(Request $request, Payment $payment)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        // Update payment status
        $payment->update([
            'status' => 'refunded',
            'refund_reason' => $request->reason,
            'refunded_at' => now(),
        ]);

        // Cancel associated subscription if exists
        if ($payment->subscription) {
            $payment->subscription->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);
        }

        return redirect()->back()
            ->with('success', 'Payment refunded successfully.');
    }
}