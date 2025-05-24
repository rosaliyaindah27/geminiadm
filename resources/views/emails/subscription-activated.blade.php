@extends('emails.layout')

@section('title', 'Subscription Activated')

@section('content')
<h2>Your Subscription is Now Active! 🚀</h2>

<p>Great news, {{ $user->name }}! Your subscription has been successfully activated.</p>

<div class="alert alert-success">
    <strong>Subscription Details:</strong><br>
    Plan: <span class="highlight">{{ $subscription->plan->name ?? 'Premium Plan' }}</span><br>
    Status: <span class="highlight">Active</span><br>
    Next Billing: <span class="highlight">{{ $subscription->next_billing_date ?? 'N/A' }}</span>
</div>

<h3>What's Included in Your Plan</h3>

<p>You now have access to:</p>

<ul>
    <li>✅ <strong>Unlimited Gemini RealTime Signals</strong> - Get instant market analysis</li>
    <li>✅ <strong>Expert Trading Signals</strong> - Curated by professional traders</li>
    <li>✅ <strong>Advanced Trading Bots</strong> - Automate your trading strategies</li>
    <li>✅ <strong>Priority Customer Support</strong> - Get help when you need it</li>
    <li>✅ <strong>Exclusive Market Reports</strong> - Weekly insights and analysis</li>
    <li>✅ <strong>Risk Management Tools</strong> - Protect your investments</li>
</ul>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ url('/dashboard') }}" class="button">Start Trading Now</a>
</div>

<h3>Getting the Most from Your Subscription</h3>

<p>To maximize your trading success:</p>

<ol>
    <li><strong>Set up your trading preferences</strong> in your dashboard</li>
    <li><strong>Configure notifications</strong> to stay updated on new signals</li>
    <li><strong>Download and configure trading bots</strong> for automated trading</li>
    <li><strong>Review our educational resources</strong> to improve your trading skills</li>
</ol>

<div class="alert alert-info">
    <strong>Pro Tip:</strong> Start with smaller position sizes while you get familiar with our signals and tools. You can always scale up as you gain confidence!
</div>

<h3>Need Support?</h3>

<p>Our premium support team is ready to help you succeed:</p>
<ul>
    <li>📧 Email: <a href="mailto:support@geminiprotrader.com">support@geminiprotrader.com</a></li>
    <li>💬 Live Chat: Available 24/7 in your dashboard</li>
    <li>📚 Knowledge Base: Comprehensive guides and tutorials</li>
</ul>

<div class="divider"></div>

<p>Thank you for choosing Gemini Pro Trader. We're excited to be part of your trading journey!</p>

<p>Happy Trading,<br>
<strong>The Gemini Pro Trader Team</strong></p>
@endsection