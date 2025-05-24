<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Complete Payment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Payment Status -->
                    <div class="mb-6">
                        @if($payment->status === 'waiting')
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">Payment Pending</h3>
                                        <p class="text-sm text-yellow-700 mt-1">Please complete your cryptocurrency payment to activate your subscription.</p>
                                    </div>
                                </div>
                            </div>
                        @elseif($payment->status === 'completed')
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-green-800">Payment Completed</h3>
                                        <p class="text-sm text-green-700 mt-1">Your payment has been confirmed and your subscription is now active!</p>
                                    </div>
                                </div>
                            </div>
                        @elseif($payment->status === 'failed')
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">Payment Failed</h3>
                                        <p class="text-sm text-red-700 mt-1">Your payment could not be processed. Please try again or contact support.</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Payment Details -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Details</h3>
                            
                            <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Payment ID:</span>
                                    <span class="font-mono text-sm">{{ $payment->nowpayments_payment_id }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Amount:</span>
                                    <span class="font-semibold">${{ number_format($payment->amount, 2) }} USD</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Pay Amount:</span>
                                    <span class="font-semibold">{{ $payment->pay_amount }} {{ strtoupper($payment->pay_currency) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Status:</span>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        @if($payment->status === 'completed') bg-green-100 text-green-800
                                        @elseif($payment->status === 'waiting') bg-yellow-100 text-yellow-800
                                        @elseif($payment->status === 'failed') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </div>
                                @if($payment->payment_updated_at)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Last Updated:</span>
                                        <span class="text-sm">{{ $payment->payment_updated_at->format('M d, Y H:i') }}</span>
                                    </div>
                                @endif
                            </div>

                            @if($payment->subscription)
                                <div class="mt-6">
                                    <h4 class="text-md font-semibold text-gray-900 mb-3">Subscription Details</h4>
                                    <div class="bg-blue-50 rounded-lg p-4">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h5 class="font-semibold text-blue-900">{{ $payment->subscription->subscriptionPlan->name }}</h5>
                                                <p class="text-blue-700 text-sm">{{ $payment->subscription->subscriptionPlan->description }}</p>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-lg font-bold text-blue-900">${{ number_format($payment->subscription->subscriptionPlan->price, 2) }}</div>
                                                <div class="text-sm text-blue-700">per {{ $payment->subscription->subscriptionPlan->billing_cycle }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Payment Instructions -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Instructions</h3>
                            
                            @if($payment->status === 'waiting')
                                <div class="bg-white border border-gray-200 rounded-lg p-4">
                                    <div class="space-y-4">
                                        @if($payment->pay_address)
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Send {{ strtoupper($payment->pay_currency) }} to this address:
                                                </label>
                                                <div class="bg-gray-100 p-3 rounded border">
                                                    <div class="flex items-center justify-between">
                                                        <code class="text-sm break-all">{{ $payment->pay_address }}</code>
                                                        <button onclick="copyToClipboard('{{ $payment->pay_address }}')" 
                                                                class="ml-2 px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                                            Copy
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="bg-yellow-50 border border-yellow-200 rounded p-3">
                                            <h4 class="font-medium text-yellow-800 mb-2">Important Notes:</h4>
                                            <ul class="text-sm text-yellow-700 space-y-1">
                                                <li>• Send exactly <strong>{{ $payment->pay_amount }} {{ strtoupper($payment->pay_currency) }}</strong></li>
                                                <li>• Payment will be confirmed automatically</li>
                                                <li>• Do not send from an exchange wallet</li>
                                                <li>• This page will update when payment is received</li>
                                            </ul>
                                        </div>

                                        <div class="text-center">
                                            <button onclick="checkPaymentStatus()" 
                                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                                Check Payment Status
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @elseif($payment->status === 'completed')
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <h4 class="font-medium text-green-800 mb-3">Payment Successful!</h4>
                                    <p class="text-green-700 mb-4">Your subscription has been activated. You can now access all premium features.</p>
                                    <div class="space-y-2">
                                        <a href="{{ route('dashboard') }}" 
                                           class="block w-full text-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                            Go to Dashboard
                                        </a>
                                        <a href="{{ route('bots.index') }}" 
                                           class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                            Download Trading Bots
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                    <h4 class="font-medium text-gray-800 mb-3">Payment Status</h4>
                                    <p class="text-gray-600 mb-4">Your payment is being processed. Please wait for confirmation.</p>
                                    <button onclick="checkPaymentStatus()" 
                                            class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                                        Refresh Status
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-8 flex justify-between">
                        <a href="{{ route('billing.index') }}" 
                           class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                            ← Back to Billing
                        </a>
                        
                        @if($payment->status === 'failed')
                            <a href="{{ route('billing.index') }}" 
                               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Try Again
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Address copied to clipboard!');
            });
        }

        function checkPaymentStatus() {
            window.location.reload();
        }

        // Auto-refresh every 30 seconds if payment is waiting
        @if($payment->status === 'waiting')
            setInterval(function() {
                window.location.reload();
            }, 30000);
        @endif
    </script>
</x-app-layout>