@extends('layouts.admin')

@section('title', 'General Settings')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">General Settings</h1>
            <p class="text-gray-600">Configure basic site settings and preferences</p>
        </div>
    </div>

    <!-- Settings Form -->
    <div class="bg-white rounded-lg shadow">
        <form method="POST" action="{{ route('admin.settings.general.update') }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Site Information -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Site Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="site_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Site Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="site_name" 
                               name="site_name" 
                               value="{{ old('site_name', $settings['site_name'] ?? 'Gemini Pro Trader') }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('site_name') border-red-500 @enderror">
                        @error('site_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="site_url" class="block text-sm font-medium text-gray-700 mb-2">
                            Site URL <span class="text-red-500">*</span>
                        </label>
                        <input type="url" 
                               id="site_url" 
                               name="site_url" 
                               value="{{ old('site_url', $settings['site_url'] ?? config('app.url')) }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('site_url') border-red-500 @enderror">
                        @error('site_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="admin_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Admin Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               id="admin_email" 
                               name="admin_email" 
                               value="{{ old('admin_email', $settings['admin_email'] ?? 'admin@geminiprotrader.com') }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('admin_email') border-red-500 @enderror">
                        @error('admin_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Contact Email
                        </label>
                        <input type="email" 
                               id="contact_email" 
                               name="contact_email" 
                               value="{{ old('contact_email', $settings['contact_email'] ?? 'support@geminiprotrader.com') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('contact_email') border-red-500 @enderror">
                        @error('contact_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Localization -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Localization</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">
                            Timezone <span class="text-red-500">*</span>
                        </label>
                        <select id="timezone" 
                                name="timezone" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('timezone') border-red-500 @enderror">
                            @php
                                $timezones = [
                                    'UTC' => 'UTC',
                                    'America/New_York' => 'Eastern Time (US & Canada)',
                                    'America/Chicago' => 'Central Time (US & Canada)',
                                    'America/Denver' => 'Mountain Time (US & Canada)',
                                    'America/Los_Angeles' => 'Pacific Time (US & Canada)',
                                    'Europe/London' => 'London',
                                    'Europe/Paris' => 'Paris',
                                    'Europe/Berlin' => 'Berlin',
                                    'Asia/Tokyo' => 'Tokyo',
                                    'Asia/Shanghai' => 'Shanghai',
                                    'Asia/Singapore' => 'Singapore',
                                    'Australia/Sydney' => 'Sydney',
                                ];
                                $currentTimezone = old('timezone', $settings['timezone'] ?? config('app.timezone'));
                            @endphp
                            @foreach($timezones as $value => $label)
                                <option value="{{ $value }}" {{ $currentTimezone === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('timezone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="default_currency" class="block text-sm font-medium text-gray-700 mb-2">
                            Default Currency <span class="text-red-500">*</span>
                        </label>
                        <select id="default_currency" 
                                name="default_currency" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('default_currency') border-red-500 @enderror">
                            @php
                                $currencies = [
                                    'USD' => 'USD - US Dollar',
                                    'EUR' => 'EUR - Euro',
                                    'GBP' => 'GBP - British Pound',
                                    'JPY' => 'JPY - Japanese Yen',
                                    'CAD' => 'CAD - Canadian Dollar',
                                    'AUD' => 'AUD - Australian Dollar',
                                    'CHF' => 'CHF - Swiss Franc',
                                    'CNY' => 'CNY - Chinese Yuan',
                                    'BTC' => 'BTC - Bitcoin',
                                    'ETH' => 'ETH - Ethereum',
                                ];
                                $currentCurrency = old('default_currency', $settings['default_currency'] ?? 'USD');
                            @endphp
                            @foreach($currencies as $value => $label)
                                <option value="{{ $value }}" {{ $currentCurrency === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('default_currency')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Display Settings -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Display Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="items_per_page" class="block text-sm font-medium text-gray-700 mb-2">
                            Items Per Page <span class="text-red-500">*</span>
                        </label>
                        <select id="items_per_page" 
                                name="items_per_page" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('items_per_page') border-red-500 @enderror">
                            @php
                                $itemsPerPageOptions = [10, 15, 20, 25, 50, 100];
                                $currentItemsPerPage = old('items_per_page', $settings['items_per_page'] ?? 20);
                            @endphp
                            @foreach($itemsPerPageOptions as $option)
                                <option value="{{ $option }}" {{ $currentItemsPerPage == $option ? 'selected' : '' }}>
                                    {{ $option }} items
                                </option>
                            @endforeach
                        </select>
                        @error('items_per_page')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date_format" class="block text-sm font-medium text-gray-700 mb-2">
                            Date Format
                        </label>
                        <select id="date_format" 
                                name="date_format" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @php
                                $dateFormats = [
                                    'Y-m-d' => date('Y-m-d') . ' (YYYY-MM-DD)',
                                    'm/d/Y' => date('m/d/Y') . ' (MM/DD/YYYY)',
                                    'd/m/Y' => date('d/m/Y') . ' (DD/MM/YYYY)',
                                    'M d, Y' => date('M d, Y') . ' (Mon DD, YYYY)',
                                    'F j, Y' => date('F j, Y') . ' (Month DD, YYYY)',
                                ];
                                $currentDateFormat = old('date_format', $settings['date_format'] ?? 'M d, Y');
                            @endphp
                            @foreach($dateFormats as $value => $label)
                                <option value="{{ $value }}" {{ $currentDateFormat === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Feature Toggles -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Feature Settings</h3>
                <div class="space-y-4">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="allow_registration" 
                               value="1"
                               {{ old('allow_registration', $settings['allow_registration'] ?? true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">
                            Allow new user registration
                        </span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="email_verification_required" 
                               value="1"
                               {{ old('email_verification_required', $settings['email_verification_required'] ?? true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">
                            Require email verification for new users
                        </span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="maintenance_mode" 
                               value="1"
                               {{ old('maintenance_mode', $settings['maintenance_mode'] ?? false) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">
                            Enable maintenance mode
                        </span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="api_enabled" 
                               value="1"
                               {{ old('api_enabled', $settings['api_enabled'] ?? true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">
                            Enable API access
                        </span>
                    </label>
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

    <!-- Current Settings Info -->
    <div class="bg-blue-50 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">
                    Current Configuration
                </h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Environment: {{ app()->environment() }}</li>
                        <li>Debug Mode: {{ config('app.debug') ? 'Enabled' : 'Disabled' }}</li>
                        <li>Cache Driver: {{ config('cache.default') }}</li>
                        <li>Session Driver: {{ config('session.driver') }}</li>
                        <li>Queue Driver: {{ config('queue.default') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection