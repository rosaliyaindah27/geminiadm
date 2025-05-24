@extends('layouts.admin')

@section('title', 'Payment Settings')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Payment Settings</h1>
            <p class="text-gray-600">Configure payment gateway and billing settings</p>
        </div>
    </div>

    <!-- Settings Form -->
    <div class="bg-white rounded-lg shadow">
        <form method="POST" action="{{ route('admin.settings.payment.update') }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- NowPayments Configuration -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">NowPayments Configuration</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nowpayments_api_key" class="block text-sm font-medium text-gray-700 mb-2">
                            API Key <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               id="nowpayments_api_key" 
                               name="nowpayments_api_key" 
                               value="{{ old('nowpayments_api_key', $settings['nowpayments_api_key'] ?? '') }}"
                               required
                               placeholder="Enter your NowPayments API key"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nowpayments_api_key') border-red-500 @enderror">
                        @error('nowpayments_api_key')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Your NowPayments API key from the dashboard</p>
                    </div>

                    <div>
                        <label for="nowpayments_ipn_key" class="block text-sm font-medium text-gray-700 mb-2">
                            IPN Secret Key <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               id="nowpayments_ipn_key" 
                               name="nowpayments_ipn_key" 
                               value="{{ old('nowpayments_ipn_key', $settings['nowpayments_ipn_key'] ?? '') }}"
                               required
                               placeholder="Enter your IPN secret key"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nowpayments_ipn_key') border-red-500 @enderror">
                        @error('nowpayments_ipn_key')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">IPN secret key for webhook verification</p>
                    </div>

                    <div>
                        <label for="nowpayments_public_key" class="block text-sm font-medium text-gray-700 mb-2">
                            Public Key
                        </label>
                        <input type="text" 
                               id="nowpayments_public_key" 
                               name="nowpayments_public_key" 
                               value="{{ old('nowpayments_public_key', $settings['nowpayments_public_key'] ?? '') }}"
                               placeholder="Enter your public key (optional)"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nowpayments_public_key') border-red-500 @enderror">
                        @error('nowpayments_public_key')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Public key for additional verification (optional)</p>
                    </div>

                    <div>
                        <label for="payment_timeout" class="block text-sm font-medium text-gray-700 mb-2">
                            Payment Timeout (seconds) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="payment_timeout" 
                               name="payment_timeout" 
                               value="{{ old('payment_timeout', $settings['payment_timeout'] ?? 3600) }}"
                               required
                               min="300"
                               max="86400"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('payment_timeout') border-red-500 @enderror">
                        @error('payment_timeout')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Time limit for payment completion (300-86400 seconds)</p>
                    </div>
                </div>
            </div>

            <!-- Environment Settings -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Environment Settings</h3>
                <div class="space-y-4">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="nowpayments_sandbox" 
                               value="1"
                               {{ old('nowpayments_sandbox', $settings['nowpayments_sandbox'] ?? false) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">
                            Enable Sandbox Mode
                        </span>
                    </label>
                    <p class="text-sm text-gray-500 ml-6">Use sandbox environment for testing payments</p>
                </div>
            </div>

            <!-- Webhook Configuration -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Webhook Configuration</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">IPN Callback URL</label>
                            <div class="flex items-center space-x-2">
                                <input type="text" 
                                       value="{{ config('app.url') }}/api/payments/webhook" 
                                       readonly
                                       class="flex-1 px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600">
                                <button type="button" 
                                        onclick="copyToClipboard('{{ config('app.url') }}/api/payments/webhook')"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm">
                                    Copy
                                </button>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Configure this URL in your NowPayments dashboard</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Success Redirect URL</label>
                            <div class="flex items-center space-x-2">
                                <input type="text" 
                                       value="{{ config('app.url') }}/billing/payment/success" 
                                       readonly
                                       class="flex-1 px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600">
                                <button type="button" 
                                        onclick="copyToClipboard('{{ config('app.url') }}/billing/payment/success')"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm">
                                    Copy
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cancel Redirect URL</label>
                            <div class="flex items-center space-x-2">
                                <input type="text" 
                                       value="{{ config('app.url') }}/billing/payment/cancel" 
                                       readonly
                                       class="flex-1 px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600">
                                <button type="button" 
                                        onclick="copyToClipboard('{{ config('app.url') }}/billing/payment/cancel')"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm">
                                    Copy
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Supported Currencies -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Supported Currencies</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @php
                        $supportedCurrencies = [
                            'BTC' => 'Bitcoin',
                            'ETH' => 'Ethereum',
                            'LTC' => 'Litecoin',
                            'BCH' => 'Bitcoin Cash',
                            'XRP' => 'Ripple',
                            'ADA' => 'Cardano',
                            'DOT' => 'Polkadot',
                            'LINK' => 'Chainlink',
                            'XLM' => 'Stellar',
                            'USDT' => 'Tether',
                            'USDC' => 'USD Coin',
                            'DAI' => 'Dai',
                        ];
                        $enabledCurrencies = old('enabled_currencies', $settings['enabled_currencies'] ?? array_keys($supportedCurrencies));
                    @endphp
                    @foreach($supportedCurrencies as $code => $name)
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <input type="checkbox" 
                                   name="enabled_currencies[]" 
                                   value="{{ $code }}"
                                   {{ in_array($code, $enabledCurrencies) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm">
                                <div class="font-medium">{{ $code }}</div>
                                <div class="text-gray-500 text-xs">{{ $name }}</div>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Test Connection -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Test Connection</h3>
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-blue-900">Test NowPayments API</h4>
                            <p class="text-sm text-blue-700">Verify your API credentials and connection</p>
                        </div>
                        <button type="button" 
                                onclick="testConnection()"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                            Test Connection
                        </button>
                    </div>
                    <div id="test-result" class="mt-3 hidden">
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
    <div class="bg-yellow-50 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">
                    Important Security Notes
                </h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Keep your API keys secure and never share them publicly</li>
                        <li>Use sandbox mode for testing before going live</li>
                        <li>Regularly rotate your API keys for security</li>
                        <li>Monitor webhook logs for any suspicious activity</li>
                        <li>Ensure your server supports HTTPS for webhook callbacks</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const button = event.target;
        const originalText = button.textContent;
        button.textContent = 'Copied!';
        button.classList.add('bg-green-600');
        button.classList.remove('bg-blue-600');
        
        setTimeout(() => {
            button.textContent = originalText;
            button.classList.remove('bg-green-600');
            button.classList.add('bg-blue-600');
        }, 2000);
    });
}

function testConnection() {
    const button = event.target;
    const resultDiv = document.getElementById('test-result');
    
    // Show loading state
    button.disabled = true;
    button.textContent = 'Testing...';
    resultDiv.classList.remove('hidden');
    resultDiv.innerHTML = '<div class="text-blue-700">Testing connection...</div>';
    
    // Make API call to test connection
    fetch('/admin/settings/test-payment-connection', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            api_key: document.getElementById('nowpayments_api_key').value,
            sandbox: document.querySelector('input[name="nowpayments_sandbox"]').checked
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = `
                <div class="text-green-700">
                    <div class="font-medium">✓ Connection successful!</div>
                    <div class="text-sm mt-1">${data.message}</div>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="text-red-700">
                    <div class="font-medium">✗ Connection failed</div>
                    <div class="text-sm mt-1">${data.message}</div>
                </div>
            `;
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `
            <div class="text-red-700">
                <div class="font-medium">✗ Connection error</div>
                <div class="text-sm mt-1">Failed to test connection</div>
            </div>
        `;
    })
    .finally(() => {
        button.disabled = false;
        button.textContent = 'Test Connection';
    });
}
</script>
@endsection