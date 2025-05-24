@extends('layouts.admin')

@section('title', 'Email Settings')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Email Settings</h1>
            <p class="text-gray-600">Configure SMTP and email delivery settings</p>
        </div>
    </div>

    <!-- Settings Form -->
    <div class="bg-white rounded-lg shadow">
        <form method="POST" action="{{ route('admin.settings.email.update') }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Mail Driver Configuration -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Mail Driver Configuration</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="mail_driver" class="block text-sm font-medium text-gray-700 mb-2">
                            Mail Driver <span class="text-red-500">*</span>
                        </label>
                        <select id="mail_driver" 
                                name="mail_driver" 
                                required
                                onchange="toggleDriverFields()"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('mail_driver') border-red-500 @enderror">
                            @php
                                $drivers = [
                                    'smtp' => 'SMTP',
                                    'sendmail' => 'Sendmail',
                                    'mailgun' => 'Mailgun',
                                    'ses' => 'Amazon SES',
                                    'postmark' => 'Postmark',
                                ];
                                $currentDriver = old('mail_driver', $settings['mail_driver'] ?? 'smtp');
                            @endphp
                            @foreach($drivers as $value => $label)
                                <option value="{{ $value }}" {{ $currentDriver === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('mail_driver')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mail_from_name" class="block text-sm font-medium text-gray-700 mb-2">
                            From Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="mail_from_name" 
                               name="mail_from_name" 
                               value="{{ old('mail_from_name', $settings['mail_from_name'] ?? 'Gemini Pro Trader') }}"
                               required
                               placeholder="Your application name"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('mail_from_name') border-red-500 @enderror">
                        @error('mail_from_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mail_from_address" class="block text-sm font-medium text-gray-700 mb-2">
                            From Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               id="mail_from_address" 
                               name="mail_from_address" 
                               value="{{ old('mail_from_address', $settings['mail_from_address'] ?? 'noreply@geminiprotrader.com') }}"
                               required
                               placeholder="noreply@yourdomain.com"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('mail_from_address') border-red-500 @enderror">
                        @error('mail_from_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- SMTP Configuration -->
            <div id="smtp-config" class="{{ $currentDriver !== 'smtp' ? 'hidden' : '' }}">
                <h3 class="text-lg font-medium text-gray-900 mb-4">SMTP Configuration</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="mail_host" class="block text-sm font-medium text-gray-700 mb-2">
                            SMTP Host <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="mail_host" 
                               name="mail_host" 
                               value="{{ old('mail_host', $settings['mail_host'] ?? 'mail.geminiprotrader.com') }}"
                               placeholder="mail.yourdomain.com"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('mail_host') border-red-500 @enderror">
                        @error('mail_host')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mail_port" class="block text-sm font-medium text-gray-700 mb-2">
                            SMTP Port <span class="text-red-500">*</span>
                        </label>
                        <select id="mail_port" 
                                name="mail_port" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('mail_port') border-red-500 @enderror">
                            @php
                                $ports = [
                                    '25' => '25 (Standard)',
                                    '465' => '465 (SSL)',
                                    '587' => '587 (TLS)',
                                    '2525' => '2525 (Alternative)',
                                ];
                                $currentPort = old('mail_port', $settings['mail_port'] ?? '465');
                            @endphp
                            @foreach($ports as $value => $label)
                                <option value="{{ $value }}" {{ $currentPort == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('mail_port')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mail_username" class="block text-sm font-medium text-gray-700 mb-2">
                            SMTP Username <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="mail_username" 
                               name="mail_username" 
                               value="{{ old('mail_username', $settings['mail_username'] ?? 'noreply@geminiprotrader.com') }}"
                               placeholder="your-email@domain.com"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('mail_username') border-red-500 @enderror">
                        @error('mail_username')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mail_password" class="block text-sm font-medium text-gray-700 mb-2">
                            SMTP Password
                        </label>
                        <input type="password" 
                               id="mail_password" 
                               name="mail_password" 
                               value="{{ old('mail_password', $settings['mail_password'] ?? '') }}"
                               placeholder="Enter password (leave blank to keep current)"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('mail_password') border-red-500 @enderror">
                        @error('mail_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Leave blank to keep current password</p>
                    </div>

                    <div>
                        <label for="mail_encryption" class="block text-sm font-medium text-gray-700 mb-2">
                            Encryption
                        </label>
                        <select id="mail_encryption" 
                                name="mail_encryption" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('mail_encryption') border-red-500 @enderror">
                            @php
                                $encryptions = [
                                    '' => 'None',
                                    'tls' => 'TLS',
                                    'ssl' => 'SSL',
                                ];
                                $currentEncryption = old('mail_encryption', $settings['mail_encryption'] ?? 'ssl');
                            @endphp
                            @foreach($encryptions as $value => $label)
                                <option value="{{ $value }}" {{ $currentEncryption === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('mail_encryption')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Email Templates Configuration -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Email Templates</h3>
                <div class="space-y-4">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="use_custom_templates" 
                               value="1"
                               {{ old('use_custom_templates', $settings['use_custom_templates'] ?? false) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">
                            Use custom email templates
                        </span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="email_queue_enabled" 
                               value="1"
                               {{ old('email_queue_enabled', $settings['email_queue_enabled'] ?? true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">
                            Queue emails for better performance
                        </span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="email_verification_enabled" 
                               value="1"
                               {{ old('email_verification_enabled', $settings['email_verification_enabled'] ?? true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">
                            Enable email verification for new users
                        </span>
                    </label>
                </div>
            </div>

            <!-- Test Email -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Test Email Configuration</h3>
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-blue-900">Send Test Email</h4>
                            <p class="text-sm text-blue-700">Send a test email to verify your configuration</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <input type="email" 
                                   id="test_email" 
                                   placeholder="test@example.com"
                                   class="px-3 py-2 border border-blue-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <button type="button" 
                                    onclick="sendTestEmail()"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                                Send Test
                            </button>
                        </div>
                    </div>
                    <div id="test-email-result" class="mt-3 hidden">
                        <!-- Test results will be displayed here -->
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <button type="button" 
                        onclick="window.location.reload()"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg">
                    Reset
                </button>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                    Save Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Current Configuration Info -->
    <div class="bg-green-50 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-green-800">
                    Current Email Configuration
                </h3>
                <div class="mt-2 text-sm text-green-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Driver: {{ $settings['mail_driver'] ?? 'Not configured' }}</li>
                        <li>Host: {{ $settings['mail_host'] ?? 'Not configured' }}</li>
                        <li>Port: {{ $settings['mail_port'] ?? 'Not configured' }}</li>
                        <li>Encryption: {{ $settings['mail_encryption'] ?? 'None' }}</li>
                        <li>From Address: {{ $settings['mail_from_address'] ?? 'Not configured' }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommended Settings -->
    <div class="bg-yellow-50 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">
                    Recommended Settings for Gemini Pro Trader
                </h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li><strong>Host:</strong> mail.geminiprotrader.com</li>
                        <li><strong>Port:</strong> 465 (SSL) or 587 (TLS)</li>
                        <li><strong>Username:</strong> noreply@geminiprotrader.com</li>
                        <li><strong>Encryption:</strong> SSL for port 465, TLS for port 587</li>
                        <li><strong>From Address:</strong> noreply@geminiprotrader.com</li>
                        <li><strong>From Name:</strong> Gemini Pro Trader</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleDriverFields() {
    const driver = document.getElementById('mail_driver').value;
    const smtpConfig = document.getElementById('smtp-config');
    
    if (driver === 'smtp') {
        smtpConfig.classList.remove('hidden');
        // Make SMTP fields required
        document.getElementById('mail_host').required = true;
        document.getElementById('mail_port').required = true;
        document.getElementById('mail_username').required = true;
    } else {
        smtpConfig.classList.add('hidden');
        // Remove required attribute from SMTP fields
        document.getElementById('mail_host').required = false;
        document.getElementById('mail_port').required = false;
        document.getElementById('mail_username').required = false;
    }
}

function sendTestEmail() {
    const button = event.target;
    const resultDiv = document.getElementById('test-email-result');
    const testEmail = document.getElementById('test_email').value;
    
    if (!testEmail) {
        alert('Please enter a test email address');
        return;
    }
    
    // Show loading state
    button.disabled = true;
    button.textContent = 'Sending...';
    resultDiv.classList.remove('hidden');
    resultDiv.innerHTML = '<div class="text-blue-700">Sending test email...</div>';
    
    // Make API call to send test email
    fetch('/admin/settings/test-email', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            email: testEmail,
            mail_driver: document.getElementById('mail_driver').value,
            mail_host: document.getElementById('mail_host').value,
            mail_port: document.getElementById('mail_port').value,
            mail_username: document.getElementById('mail_username').value,
            mail_password: document.getElementById('mail_password').value,
            mail_encryption: document.getElementById('mail_encryption').value,
            mail_from_address: document.getElementById('mail_from_address').value,
            mail_from_name: document.getElementById('mail_from_name').value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = `
                <div class="text-green-700">
                    <div class="font-medium">✓ Test email sent successfully!</div>
                    <div class="text-sm mt-1">Check your inbox at ${testEmail}</div>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="text-red-700">
                    <div class="font-medium">✗ Failed to send test email</div>
                    <div class="text-sm mt-1">${data.message}</div>
                </div>
            `;
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `
            <div class="text-red-700">
                <div class="font-medium">✗ Error sending test email</div>
                <div class="text-sm mt-1">Please check your configuration</div>
            </div>
        `;
    })
    .finally(() => {
        button.disabled = false;
        button.textContent = 'Send Test';
    });
}

// Initialize driver fields on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleDriverFields();
});
</script>
@endsection