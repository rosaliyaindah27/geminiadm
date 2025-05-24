@extends('emails.layout')

@section('title', 'New Expert Signal Available')

@section('content')
<h2>🎯 New Expert Signal Available!</h2>

<p>Hello {{ $user->name }},</p>

<p>Our expert traders have identified a new trading opportunity that matches your preferences.</p>

<div class="alert alert-info">
    <strong>Signal Details:</strong><br>
    Pair: <span class="highlight">{{ $signal->pair ?? 'BTC/USDT' }}</span><br>
    Action: <span class="highlight">{{ strtoupper($signal->action ?? 'BUY') }}</span><br>
    Entry Price: <span class="highlight">${{ number_format($signal->entry_price ?? 0, 4) }}</span><br>
    Confidence: <span class="highlight">{{ $signal->confidence ?? 'High' }}</span>
</div>

<h3>📊 Signal Analysis</h3>

<p><strong>Market Analysis:</strong></p>
<p>{{ $signal->analysis ?? 'Based on technical analysis and market conditions, this signal presents a favorable risk-to-reward ratio.' }}</p>

<p><strong>Risk Management:</strong></p>
<ul>
    <li>Take Profit: <span class="code">${{ number_format($signal->take_profit ?? 0, 4) }}</span></li>
    <li>Stop Loss: <span class="code">${{ number_format($signal->stop_loss ?? 0, 4) }}</span></li>
    <li>Risk Level: <span class="highlight">{{ $signal->risk_level ?? 'Medium' }}</span></li>
</ul>

<div class="alert alert-warning">
    <strong>⚠️ Important Reminder:</strong><br>
    Always do your own research and never invest more than you can afford to lose. This signal is for educational purposes and should not be considered as financial advice.
</div>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ url('/expert-signals') }}" class="button">View Full Signal Details</a>
</div>

<h3>📈 Recent Performance</h3>

<p>Our expert signals have maintained strong performance:</p>
<ul>
    <li>✅ 78% success rate over the last 30 days</li>
    <li>📊 Average return: +12.5% per successful signal</li>
    <li>⏱️ Average signal duration: 2-5 days</li>
</ul>

<h3>🔔 Notification Settings</h3>

<p>Want to customize how you receive signal notifications? You can:</p>
<ul>
    <li>Adjust notification frequency</li>
    <li>Set specific pairs you're interested in</li>
    <li>Choose your preferred risk levels</li>
</ul>

<div style="text-align: center; margin: 20px 0;">
    <a href="{{ url('/profile/notifications') }}" style="color: #1e40af; text-decoration: none;">Manage Notification Preferences</a>
</div>

<div class="divider"></div>

<p>Time-sensitive signals require quick action. Don't miss out on this opportunity!</p>

<p>Best of luck with your trades,<br>
<strong>The Gemini Pro Trader Expert Team</strong></p>

<p style="font-size: 12px; color: #6b7280; margin-top: 20px;">
    <em>This signal was generated at {{ now()->format('Y-m-d H:i:s') }} UTC. Market conditions can change rapidly, so please verify current prices before executing any trades.</em>
</p>
@endsection