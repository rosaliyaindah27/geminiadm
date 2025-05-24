@extends('layouts.admin')

@section('title', 'Security Settings')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Security Settings</h1>
            <p class="text-gray-600">Configure authentication and security policies</p>
        </div>
    </div>

    <!-- Settings Form -->
    <div class="bg-white rounded-lg shadow">
        <form method="POST" action="{{ route('admin.settings.security.update') }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Session Management -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Session Management</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="session_lifetime" class="block text-sm font-medium text-gray-700 mb-2">
                            Session Lifetime (minutes) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="session_lifetime" 
                               name="session_lifetime" 
                               value="{{ old('session_lifetime', $settings['session_lifetime'] ?? 120) }}"
                               required
                               min="1"
                               max="10080"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('session_lifetime') border-red-500 @enderror">
                        @error('session_lifetime')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">How long user sessions remain active (1-10080 minutes)</p>
                    </div>

                    <div>
                        <label for="password_timeout" class="block text-sm font-medium text-gray-700 mb-2">
                            Password Confirmation Timeout (minutes) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="password_timeout" 
                               name="password_timeout" 
                               value="{{ old('password_timeout', $settings['password_timeout'] ?? 10800) }}"
                               required
                               min="1"
                               max="1440"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password_timeout') border-red-500 @enderror">
                        @error('password_timeout')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">How long before requiring password re-confirmation (1-1440 minutes)</p>
                    </div>
                </div>
            </div>

            <!-- Login Security -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Login Security</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="max_login_attempts" class="block text-sm font-medium text-gray-700 mb-2">
                            Maximum Login Attempts <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="max_login_attempts" 
                               name="max_login_attempts" 
                               value="{{ old('max_login_attempts', $settings['max_login_attempts'] ?? 5) }}"
                               required
                               min="1"
                               max="20"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('max_login_attempts') border-red-500 @enderror">
                        @error('max_login_attempts')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Number of failed attempts before account lockout (1-20)</p>
                    </div>

                    <div>
                        <label for="lockout_duration" class="block text-sm font-medium text-gray-700 mb-2">
                            Lockout Duration (minutes) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="lockout_duration" 
                               name="lockout_duration" 
                               value="{{ old('lockout_duration', $settings['lockout_duration'] ?? 1) }}"
                               required
                               min="1"
                               max="60"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('lockout_duration') border-red-500 @enderror">
                        @error('lockout_duration')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">How long accounts remain locked after failed attempts (1-60 minutes)</p>
                    </div>
                </div>
            </div>

            <!-- Password Policy -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Password Policy</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="min_password_length" class="block text-sm font-medium text-gray-700 mb-2">
                            Minimum Password Length
                        </label>
                        <input type="number" 
                               id="min_password_length" 
                               name="min_password_length" 
                               value="{{ old('min_password_length', $settings['min_password_length'] ?? 8) }}"
                               min="6"
                               max="128"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('min_password_length') border-red-500 @enderror">
                        @error('min_password_length')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Minimum number of characters required (6-128)</p>
                    </div>

                    <div>
                        <label for="password_expiry_days" class="block text-sm font-medium text-gray-700 mb-2">
                            Password Expiry (days)
                        </label>
                        <input type="number" 
                               id="password_expiry_days" 
                               name="password_expiry_days" 
                               value="{{ old('password_expiry_days', $settings['password_expiry_days'] ?? 0) }}"
                               min="0"
                               max="365"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password_expiry_days') border-red-500 @enderror">
                        @error('password_expiry_days')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Days before password expires (0 = never expires)</p>
                    </div>
                </div>

                <div class="space-y-4 mt-4">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="require_uppercase" 
                               value="1"
                               {{ old('require_uppercase', $settings['require_uppercase'] ?? false) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">
                            Require uppercase letters
                        </span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="require_lowercase" 
                               value="1"
                               {{ old('require_lowercase', $settings['require_lowercase'] ?? false) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">
                            Require lowercase letters
                        </span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="require_numbers" 
                               value="1"
                               {{ old('require_numbers', $settings['require_numbers'] ?? false) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">
                            Require numbers
                        </span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="require_symbols" 
                               value="1"
                               {{ old('require_symbols', $settings['require_symbols'] ?? false) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">
                            Require special characters
                        </span>
                    </label>
                </div>
            </div>

            <!-- Security Features -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Security Features</h3>
                <div class="space-y-4">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="force_https" 
                               value="1"
                               {{ old('force_https', $settings['force_https'] ?? false) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">
                            Force HTTPS connections
                        </span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="two_factor_enabled" 
                               value="1"
                               {{ old('two_factor_enabled', $settings['two_factor_enabled'] ?? false) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">
                            Enable Two-Factor Authentication
                        </span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="require_email_verification" 
                               value="1"
                               {{ old('require_email_verification', $settings['require_email_verification'] ?? true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">
                            Require email verification for new accounts
                        </span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="log_failed_logins" 
                               value="1"
                               {{ old('log_failed_logins', $settings['log_failed_logins'] ?? true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">
                            Log failed login attempts
                        </span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="enable_captcha" 
                               value="1"
                               {{ old('enable_captcha', $settings['enable_captcha'] ?? false) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">
                            Enable CAPTCHA for login forms
                        </span>
                    </label>
                </div>
            </div>

            <!-- IP Restrictions -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">IP Access Control</h3>
                <div class="space-y-4">
                    <div>
                        <label for="admin_ip_whitelist" class="block text-sm font-medium text-gray-700 mb-2">
                            Admin IP Whitelist
                        </label>
                        <textarea id="admin_ip_whitelist" 
                                  name="admin_ip_whitelist" 
                                  rows="3"
                                  placeholder="Enter IP addresses, one per line (e.g., 192.168.1.1, 10.0.0.0/8)"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('admin_ip_whitelist') border-red-500 @enderror">{{ old('admin_ip_whitelist', $settings['admin_ip_whitelist'] ?? '') }}</textarea>
                        @error('admin_ip_whitelist')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Restrict admin access to specific IP addresses (leave blank to allow all)</p>
                    </div>

                    <div>
                        <label for="blocked_ips" class="block text-sm font-medium text-gray-700 mb-2">
                            Blocked IP Addresses
                        </label>
                        <textarea id="blocked_ips" 
                                  name="blocked_ips" 
                                  rows="3"
                                  placeholder="Enter IP addresses to block, one per line"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('blocked_ips') border-red-500 @enderror">{{ old('blocked_ips', $settings['blocked_ips'] ?? '') }}</textarea>
                        @error('blocked_ips')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">IP addresses that are completely blocked from accessing the site</p>
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

    <!-- Current Security Status -->
    <div class="bg-green-50 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-green-800">
                    Current Security Status
                </h3>
                <div class="mt-2 text-sm text-green-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Session Lifetime: {{ $settings['session_lifetime'] ?? 120 }} minutes</li>
                        <li>Max Login Attempts: {{ $settings['max_login_attempts'] ?? 5 }}</li>
                        <li>Lockout Duration: {{ $settings['lockout_duration'] ?? 1 }} minutes</li>
                        <li>HTTPS Enforced: {{ ($settings['force_https'] ?? false) ? 'Yes' : 'No' }}</li>
                        <li>Two-Factor Auth: {{ ($settings['two_factor_enabled'] ?? false) ? 'Enabled' : 'Disabled' }}</li>
                        <li>Email Verification: {{ ($settings['require_email_verification'] ?? true) ? 'Required' : 'Optional' }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Recommendations -->
    <div class="bg-yellow-50 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">
                    Security Recommendations
                </h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Enable HTTPS enforcement for production environments</li>
                        <li>Consider enabling Two-Factor Authentication for admin accounts</li>
                        <li>Set up IP whitelisting for admin access if possible</li>
                        <li>Regularly review and update password policies</li>
                        <li>Monitor failed login attempts and suspicious activity</li>
                        <li>Keep session lifetimes reasonable (60-240 minutes)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection