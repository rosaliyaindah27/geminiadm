<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\UserNotification;
use App\Services\NowPaymentsService;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    private NowPaymentsService $nowPayments;

    public function __construct(NowPaymentsService $nowPayments)
    {
        $this->nowPayments = $nowPayments;
    }

    public function handle(Request $request)
    {
        // Get the raw payload
        $payload = $request->getContent();
        $signature = $request->header('x-nowpayments-sig');

        // Verify the webhook signature
        if (!$this->nowPayments->verifyIpnSignature($payload, $signature)) {
            Log::warning('Invalid webhook signature received');
            return response('Invalid signature', 400);
        }

        $data = json_decode($payload, true);
        
        if (!$data) {
            Log::error('Invalid JSON payload received');
            return response('Invalid JSON', 400);
        }

        Log::info('Payment webhook received', $data);

        try {
            $this->processPaymentUpdate($data);
            return response('OK', 200);
        } catch (\Exception $e) {
            Log::error('Error processing payment webhook: ' . $e->getMessage(), [
                'data' => $data,
                'exception' => $e
            ]);
            return response('Error processing webhook', 500);
        }
    }

    private function processPaymentUpdate(array $data): void
    {
        $paymentId = $data['payment_id'] ?? null;
        $paymentStatus = $data['payment_status'] ?? null;

        if (!$paymentId || !$paymentStatus) {
            throw new \Exception('Missing payment_id or payment_status in webhook data');
        }

        // Find the payment record
        $payment = Payment::where('nowpayments_payment_id', $paymentId)->first();

        if (!$payment) {
            Log::warning('Payment not found for webhook', ['payment_id' => $paymentId]);
            return;
        }

        // Update payment record
        $payment->update([
            'payment_status' => $paymentStatus,
            'actually_paid' => $data['actually_paid'] ?? $payment->actually_paid,
            'outcome_amount' => $data['outcome_amount'] ?? $payment->outcome_amount,
            'outcome_currency' => $data['outcome_currency'] ?? $payment->outcome_currency,
            'payment_extra' => $data,
            'payment_updated_at' => now(),
        ]);

        // Handle different payment statuses
        switch ($paymentStatus) {
            case 'finished':
                $this->handleSuccessfulPayment($payment);
                break;
                
            case 'failed':
            case 'expired':
                $this->handleFailedPayment($payment);
                break;
                
            case 'partially_paid':
                $this->handlePartialPayment($payment);
                break;
                
            case 'refunded':
                $this->handleRefundedPayment($payment);
                break;
        }
    }

    private function handleSuccessfulPayment(Payment $payment): void
    {
        if (!$payment->subscription) {
            return;
        }

        $subscription = $payment->subscription;
        
        // Activate the subscription
        $subscription->update([
            'status' => 'active',
            'starts_at' => now(),
        ]);

        // Create success notification
        UserNotification::createForUser(
            $payment->user,
            'payment',
            'Payment Successful',
            "Your payment of {$payment->formatted_price_amount} has been processed successfully. Your subscription is now active!",
            [
                'payment_id' => $payment->id,
                'subscription_id' => $subscription->id,
            ],
            'high',
            route('billing.index')
        );

        Log::info('Subscription activated', [
            'user_id' => $payment->user_id,
            'subscription_id' => $subscription->id,
            'payment_id' => $payment->id,
        ]);
    }

    private function handleFailedPayment(Payment $payment): void
    {
        if (!$payment->subscription) {
            return;
        }

        $subscription = $payment->subscription;
        
        // Mark subscription as failed
        $subscription->update([
            'status' => 'failed',
        ]);

        // Create failure notification
        UserNotification::createForUser(
            $payment->user,
            'payment',
            'Payment Failed',
            "Your payment of {$payment->formatted_price_amount} has failed. Please try again or contact support.",
            [
                'payment_id' => $payment->id,
                'subscription_id' => $subscription->id,
            ],
            'high',
            route('billing.index')
        );

        Log::info('Payment failed', [
            'user_id' => $payment->user_id,
            'payment_id' => $payment->id,
            'status' => $payment->payment_status,
        ]);
    }

    private function handlePartialPayment(Payment $payment): void
    {
        // Create partial payment notification
        UserNotification::createForUser(
            $payment->user,
            'payment',
            'Partial Payment Received',
            "We've received a partial payment of {$payment->formatted_pay_amount}. Please complete the remaining amount to activate your subscription.",
            ['payment_id' => $payment->id],
            'medium',
            route('billing.payment', $payment->nowpayments_payment_id)
        );

        Log::info('Partial payment received', [
            'user_id' => $payment->user_id,
            'payment_id' => $payment->id,
            'actually_paid' => $payment->actually_paid,
            'expected_amount' => $payment->pay_amount,
        ]);
    }

    private function handleRefundedPayment(Payment $payment): void
    {
        if (!$payment->subscription) {
            return;
        }

        $subscription = $payment->subscription;
        
        // Cancel the subscription if it was active
        if ($subscription->status === 'active') {
            $subscription->cancel();
        }

        // Create refund notification
        UserNotification::createForUser(
            $payment->user,
            'payment',
            'Payment Refunded',
            "Your payment of {$payment->formatted_price_amount} has been refunded. Your subscription has been cancelled.",
            [
                'payment_id' => $payment->id,
                'subscription_id' => $subscription->id,
            ],
            'medium',
            route('billing.index')
        );

        Log::info('Payment refunded', [
            'user_id' => $payment->user_id,
            'payment_id' => $payment->id,
            'subscription_id' => $subscription->id,
        ]);
    }
}
