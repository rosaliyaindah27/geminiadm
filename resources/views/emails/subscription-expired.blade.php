@extends('emails.layout')

@section('title', 'Subscription Expired')

@section('content')
<h2>Your Subscription Has Expired</h2>

<p>Hello {{ $user->name }},</p>

<p>We wanted to let you know that your Gemini Pro Trader subscription has expired.</p>

<div class="alert alert-warning">
    <strong>Subscription Details:</strong><br>
    Plan: <span class="highlight">{{ $subscription->plan->name ?? 'Premium Plan' }}</span><br>
    Expired On: <span class="highlight">{{ $subscription->expires_at->format('M d, Y') ?? 'Recently' }}</span><br>
    Status: <span class="highlight">Expired</span>
</div>

<h3>What This Means</h3>

<p>With an expired subscription, your account has been moved to our free tier with limited access:</p>

<ul>
    <li>❌ <strong>Limited Signals:</strong> Access to only 3 signals per day</li>
    <li>❌ <strong>No Expert Signals:</strong> Premium signals are no longer available</li>
    <li>❌ <strong>No Trading Bots:</strong> Bot downloads are disabled</li>
    <li>❌ <strong>Basic Support:</strong> Email support only (no priority support)</li>
    <li>✅ <strong>Account Access:</strong> You can still access your dashboard</li>
    <li>✅ <strong>Historical Data:</strong> Your past signals and data remain available</li>
</ul>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ url('/billing/plans') }}" class="button">Renew Your Subscription</a>
</div>

<h3>🎯 Why Renew?</h3>

<p>Don't miss out on the trading opportunities that our premium features provide:</p>

<div class="alert alert-info">
    <strong>Premium Benefits You're Missing:</strong>
    <ul style="margin: 10px 0; padding-left: 20px;">
        <li>Unlimited high-quality trading signals</li>
        <li>Expert-curated signals with 78% success rate</li>
        <li>Advanced trading bots for automation</li>
        <li>Real-time market analysis and insights</li>
        <li>Priority customer support</li>
        <li>Exclusive webinars and educational content</li>
    </ul>
</div>

<h3>💰 Special Renewal Offer</h3>

<p>As a valued former subscriber, we're offering you a special discount to renew:</p>

<div class="alert alert-success">
    <strong>🎉 Limited Time Offer:</strong><br>
    Get <span class="highlight">20% OFF</span> your next subscription when you renew within the next 7 days!<br>
    Use code: <span class="code">WELCOME20</span>
</div>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ url('/billing/plans?discount=WELCOME20') }}" class="button">Claim Your Discount</a>
</div>

<h3>📈 What You've Achieved</h3>

<p>During your subscription period, you had access to:</p>

<ul>
    <li>📊 <strong>{{ $stats['signals_received'] ?? 'Many' }} trading signals</strong> delivered to your account</li>
    <li>🤖 <strong>{{ $stats['bots_downloaded'] ?? 'Several' }} trading bots</strong> downloaded and configured</li>
    <li>💹 <strong>Potential profits</strong> from our expert recommendations</li>
    <li>📚 <strong>Educational resources</strong> to improve your trading skills</li>
</ul>

<h3>🔄 Easy Renewal Process</h3>

<p>Renewing your subscription is quick and easy:</p>

<ol>
    <li>Visit your <a href="{{ url('/billing') }}">billing dashboard</a></li>
    <li>Choose your preferred plan</li>
    <li>Complete the payment process</li>
    <li>Instant access to all premium features</li>
</ol>

<h3>💬 Need Help Deciding?</h3>

<p>Our team is here to help you choose the right plan:</p>

<ul>
    <li>📧 Email: <a href="mailto:support@geminiprotrader.com">support@geminiprotrader.com</a></li>
    <li>💬 Live Chat: Available in your dashboard</li>
    <li>📞 Phone: +1 (555) 123-4567</li>
</ul>

<p>We can also help you:</p>
<ul>
    <li>Review your trading performance during your subscription</li>
    <li>Recommend the best plan for your trading style</li>
    <li>Answer any questions about our features</li>
</ul>

<div class="alert alert-warning">
    <strong>⏰ Don't Wait Too Long:</strong><br>
    The cryptocurrency market moves fast, and every day without premium signals could mean missed opportunities. Our subscribers often see their best trades come from our expert signals.
</div>

<div class="divider"></div>

<p>We hope to welcome you back to our premium community soon. Thank you for being part of the Gemini Pro Trader family!</p>

<p>Best regards,<br>
<strong>The Gemini Pro Trader Team</strong></p>

<p style="font-size: 12px; color: #6b7280; margin-top: 20px;">
    <em>You're receiving this email because your subscription has expired. If you don't wish to receive renewal reminders, you can <a href="#">unsubscribe here</a>.</em>
</p>
@endsection