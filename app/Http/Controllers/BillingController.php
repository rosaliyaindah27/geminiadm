<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\Payment;
use App\Services\NowPaymentsService;
use App\Models\UserNotification;

class BillingController extends Controller
{
    private NowPaymentsService $nowPayments;

    public function __construct(NowPaymentsService $nowPayments)
    {
        $this->nowPayments = $nowPayments;
    }

    public function index()
    {
        $user = auth()->user();
        $activeSubscription = $user->activeSubscription;
        $subscriptionPlans = SubscriptionPlan::where('is_active', true)->get();
        $recentPayments = $user->payments()->latest()->limit(10)->get();

        return view('billing.index', compact(
            'activeSubscription',
            'subscriptionPlans',
            'recentPayments'
        ));
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'pay_currency' => 'required|string|in:btc,eth,ltc,usdt',
        ]);

        $user = auth()->user();
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        // Check if user already has an active subscription
        if ($user->hasActiveSubscription()) {
            return redirect()
                ->route('billing.index')
                ->with('error', 'You already have an active subscription.');
        }

        try {
            // Create payment with NowPayments
            $paymentResponse = $this->nowPayments->createSubscriptionPayment(
                $user->id,
                $plan->id,
                $plan->price,
                $plan->currency,
                $request->pay_currency
            );

            // Create subscription record
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'status' => 'pending',
                'starts_at' => now(),
                'ends_at' => now()->addMonth($plan->billing_period === 'yearly' ? 12 : 1),
                'amount_paid' => $plan->price,
                'currency' => $plan->currency,
                'payment_method' => 'crypto',
            ]);

            // Create payment record
            Payment::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'nowpayments_payment_id' => $paymentResponse['payment_id'],
                'nowpayments_order_id' => $paymentResponse['order_id'] ?? null,
                'payment_status' => $paymentResponse['payment_status'],
                'pay_amount' => $paymentResponse['pay_amount'],
                'pay_currency' => $paymentResponse['pay_currency'],
                'price_amount' => $paymentResponse['price_amount'],
                'price_currency' => $paymentResponse['price_currency'],
                'pay_address' => $paymentResponse['pay_address'] ?? null,
                'description' => "Subscription to {$plan->name}",
                'payment_created_at' => now(),
            ]);

            return redirect($paymentResponse['invoice_url'] ?? route('billing.payment', $paymentResponse['payment_id']));

        } catch (\Exception $e) {
            return redirect()
                ->route('billing.index')
                ->with('error', 'Failed to create payment: ' . $e->getMessage());
        }
    }

    public function payment(string $paymentId)
    {
        $payment = Payment::where('nowpayments_payment_id', $paymentId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        try {
            // Get updated payment status from NowPayments
            $paymentStatus = $this->nowPayments->getPaymentStatus($paymentId);
            
            // Update local payment record
            $payment->update([
                'payment_status' => $paymentStatus['payment_status'],
                'actually_paid' => $paymentStatus['actually_paid'] ?? null,
                'outcome_amount' => $paymentStatus['outcome_amount'] ?? null,
                'outcome_currency' => $paymentStatus['outcome_currency'] ?? null,
                'payment_updated_at' => now(),
            ]);

        } catch (\Exception $e) {
            // Log error but continue to show payment page
            \Log::error('Failed to update payment status: ' . $e->getMessage());
        }

        return view('billing.payment', compact('payment'));
    }

    public function paymentHistory()
    {
        $payments = auth()->user()->payments()
            ->with('subscription.subscriptionPlan')
            ->latest()
            ->paginate(15);

        return view('billing.history', compact('payments'));
    }

    public function cancelSubscription()
    {
        $user = auth()->user();
        $subscription = $user->activeSubscription;

        if (!$subscription) {
            return redirect()
                ->route('billing.index')
                ->with('error', 'No active subscription found.');
        }

        $subscription->cancel();

        // Create notification
        UserNotification::createForUser(
            $user,
            'subscription',
            'Subscription Cancelled',
            'Your subscription has been cancelled. You will continue to have access until the end of your billing period.',
            ['subscription_id' => $subscription->id],
            'medium'
        );

        return redirect()
            ->route('billing.index')
            ->with('success', 'Subscription cancelled successfully.');
    }

    public function success()
    {
        return view('billing.success');
    }

    public function cancel()
    {
        return view('billing.cancel');
    }

    public function downloadInvoice(Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        // Generate and return PDF invoice
        // This would typically use a PDF library like DomPDF or similar
        return response()->json(['message' => 'Invoice download feature coming soon']);
    }
}
