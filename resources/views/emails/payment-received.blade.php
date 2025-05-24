@extends('emails.layout')

@section('title', 'Payment Received')

@section('content')
<h2>Payment Received Successfully! 💳</h2>

<p>Hello {{ $user->name }},</p>

<p>We have successfully received your payment. Thank you for your continued trust in Gemini Pro Trader!</p>

<div class="alert alert-success">
    <strong>Payment Details:</strong><br>
    Amount: <span class="highlight">${{ number_format($payment->amount ?? 0, 2) }}</span><br>
    Transaction ID: <span class="code">{{ $payment->transaction_id ?? 'N/A' }}</span><br>
    Payment Method: <span class="highlight">{{ ucfirst($payment->payment_method ?? 'Cryptocurrency') }}</span><br>
    Date: <span class="highlight">{{ $payment->created_at->format('M d, Y H:i') ?? now()->format('M d, Y H:i') }}</span>
</div>

<h3>What Happens Next?</h3>

<p>Your payment has been processed and your account will be updated within the next few minutes:</p>

<ul>
    <li>✅ <strong>Subscription Activation:</strong> Your subscription will be activated automatically</li>
    <li>✅ <strong>Feature Access:</strong> All premium features will be unlocked</li>
    <li>✅ <strong>Email Confirmation:</strong> You'll receive a separate confirmation email</li>
    <li>✅ <strong>Receipt:</strong> Your receipt is attached to this email</li>
</ul>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ url('/dashboard') }}" class="button">Access Your Dashboard</a>
</div>

<h3>📊 Your Subscription Benefits</h3>

<p>With your active subscription, you now have access to:</p>

<div class="alert alert-info">
    <ul style="margin: 0; padding-left: 20px;">
        <li>Unlimited Gemini RealTime Signals</li>
        <li>Expert Trading Signals from professionals</li>
        <li>Advanced Trading Bot downloads</li>
        <li>Priority customer support</li>
        <li>Exclusive market analysis reports</li>
        <li>Risk management tools</li>
    </ul>
</div>

<h3>💡 Getting Started Tips</h3>

<p>To make the most of your subscription:</p>

<ol>
    <li><strong>Set up your trading preferences</strong> in your dashboard</li>
    <li><strong>Configure notification settings</strong> to receive signals via email or push notifications</li>
    <li><strong>Download trading bots</strong> and configure them with your exchange API keys</li>
    <li><strong>Join our community</strong> to connect with other traders</li>
</ol>

<h3>🔒 Security & Privacy</h3>

<p>Your payment information is secure and encrypted. We use industry-standard security measures to protect your data and never store sensitive payment details on our servers.</p>

<div class="alert alert-warning">
    <strong>Important:</strong> Keep this email as your receipt. If you need to contact support about this payment, please reference the transaction ID above.
</div>

<h3>Need Help?</h3>

<p>If you have any questions about your payment or subscription:</p>

<ul>
    <li>📧 Email: <a href="mailto:billing@geminiprotrader.com">billing@geminiprotrader.com</a></li>
    <li>💬 Live Chat: Available 24/7 in your dashboard</li>
    <li>📞 Phone: +1 (555) 123-4567</li>
</ul>

<div class="divider"></div>

<p>Thank you for choosing Gemini Pro Trader. We're excited to help you achieve your trading goals!</p>

<p>Best regards,<br>
<strong>The Gemini Pro Trader Billing Team</strong></p>

<p style="font-size: 12px; color: #6b7280; margin-top: 20px;">
    <em>This is an automated receipt for your payment. Please keep this email for your records. If you believe you received this email in error, please contact our support team immediately.</em>
</p>
@endsection