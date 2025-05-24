@extends('emails.layout')

@section('title', 'Welcome to Gemini Pro Trader')

@section('content')
<h2>Welcome to Gemini Pro Trader, {{ $user->name }}! 🎉</h2>

<p>Thank you for joining our community of professional traders. We're excited to have you on board!</p>

<div class="alert alert-success">
    <strong>Your account is now active!</strong> You can start exploring our powerful trading tools and signals right away.
</div>

<h3>What's Next?</h3>

<p>Here are some things you can do to get started:</p>

<ul>
    <li><strong>Complete your profile:</strong> Add your trading preferences and risk tolerance</li>
    <li><strong>Explore Gemini RealTime Signals:</strong> Get instant market analysis and trading signals</li>
    <li><strong>Check Expert Signals:</strong> Access curated signals from our professional traders</li>
    <li><strong>Download Trading Bots:</strong> Automate your trading with our advanced bots</li>
    <li><strong>Choose a subscription plan:</strong> Unlock premium features and advanced tools</li>
</ul>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ url('/dashboard') }}" class="button">Access Your Dashboard</a>
</div>

<div class="alert alert-info">
    <strong>Need Help?</strong> Our support team is here to assist you. Feel free to reach out if you have any questions about getting started.
</div>

<h3>Important Security Tips</h3>

<p>To keep your account secure:</p>
<ul>
    <li>Never share your login credentials with anyone</li>
    <li>Enable two-factor authentication for extra security</li>
    <li>Use a strong, unique password</li>
    <li>Always log out when using shared computers</li>
</ul>

<div class="divider"></div>

<p>We're committed to helping you succeed in your trading journey. Welcome aboard!</p>

<p>Best regards,<br>
<strong>The Gemini Pro Trader Team</strong></p>
@endsection