@extends('layouts.admin')

@section('title', 'Maintenance Settings')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Maintenance Settings</h1>
            <p class="text-gray-600">Manage site maintenance mode and system status</p>
        </div>
    </div>

    <!-- Current Status -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Current Status</h3>
            
            @if($isDown ?? false)
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                Site is currently in maintenance mode
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>The site is not accessible to regular users. Only administrators can access the admin panel.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800">
                                Site is currently online
                            </h3>
                            <div class="mt-2 text-sm text-green-700">
                                <p>The site is accessible to all users and functioning normally.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Maintenance Controls -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Maintenance Controls</h3>
            
            <form method="POST" action="{{ route('admin.settings.maintenance.toggle') }}" class="space-y-6">
                @csrf
                
                @if(!($isDown ?? false))
                    <!-- Enable Maintenance Mode -->
                    <div>
                        <h4 class="text-md font-medium text-gray-900 mb-3">Enable Maintenance Mode</h4>
                        <div class="space-y-4">
                            <div>
                                <label for="secret" class="block text-sm font-medium text-gray-700 mb-2">
                                    Secret Access Key
                                </label>
                                <input type="text" 
                                       id="secret" 
                                       name="secret" 
                                       value="{{ old('secret', 'admin-secret') }}"
                                       placeholder="Enter secret key for admin access"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <p class="mt-1 text-sm text-gray-500">
                                    This key will allow admin access during maintenance. 
                                    Access URL: {{ config('app.url') }}?secret=your-secret-key
                                </p>
                            </div>
                            
                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                                    Maintenance Message (Optional)
                                </label>
                                <textarea id="message" 
                                          name="message" 
                                          rows="3"
                                          placeholder="Enter a custom message to display to users during maintenance"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('message', 'We are currently performing scheduled maintenance. Please check back soon.') }}</textarea>
                                <p class="mt-1 text-sm text-gray-500">This message will be shown to users when they visit the site.</p>
                            </div>
                            
                            <div>
                                <label for="retry_after" class="block text-sm font-medium text-gray-700 mb-2">
                                    Retry After (seconds)
                                </label>
                                <input type="number" 
                                       id="retry_after" 
                                       name="retry_after" 
                                       value="{{ old('retry_after', 3600) }}"
                                       min="60"
                                       max="86400"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <p class="mt-1 text-sm text-gray-500">
                                    Suggested time for users to retry (60-86400 seconds). This sets the HTTP Retry-After header.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" 
                                class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg">
                            Enable Maintenance Mode
                        </button>
                    </div>
                @else
                    <!-- Disable Maintenance Mode -->
                    <div>
                        <h4 class="text-md font-medium text-gray-900 mb-3">Disable Maintenance Mode</h4>
                        <p class="text-gray-600 mb-4">
                            Click the button below to bring the site back online and make it accessible to all users.
                        </p>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" 
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                            Disable Maintenance Mode
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- System Information -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">System Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Laravel Version</p>
                            <p class="text-lg font-semibold text-gray-900">{{ app()->version() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">PHP Version</p>
                            <p class="text-lg font-semibold text-gray-900">{{ PHP_VERSION }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Environment</p>
                            <p class="text-lg font-semibold text-gray-900">{{ app()->environment() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Server Time</p>
                            <p class="text-lg font-semibold text-gray-900">{{ now()->format('Y-m-d H:i:s') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Debug Mode</p>
                            <p class="text-lg font-semibold text-gray-900">{{ config('app.debug') ? 'Enabled' : 'Disabled' }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Cache Driver</p>
                            <p class="text-lg font-semibold text-gray-900">{{ config('cache.default') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <button onclick="clearCache('config')" 
                        class="bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg p-4 text-left transition-colors">
                    <div class="flex items-center">
                        <svg class="h-6 w-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <div>
                            <p class="font-medium text-blue-900">Clear Config Cache</p>
                            <p class="text-sm text-blue-700">Clear configuration cache</p>
                        </div>
                    </div>
                </button>

                <button onclick="clearCache('route')" 
                        class="bg-green-50 hover:bg-green-100 border border-green-200 rounded-lg p-4 text-left transition-colors">
                    <div class="flex items-center">
                        <svg class="h-6 w-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        <div>
                            <p class="font-medium text-green-900">Clear Route Cache</p>
                            <p class="text-sm text-green-700">Clear route cache</p>
                        </div>
                    </div>
                </button>

                <button onclick="clearCache('view')" 
                        class="bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 rounded-lg p-4 text-left transition-colors">
                    <div class="flex items-center">
                        <svg class="h-6 w-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <div>
                            <p class="font-medium text-yellow-900">Clear View Cache</p>
                            <p class="text-sm text-yellow-700">Clear compiled views</p>
                        </div>
                    </div>
                </button>

                <button onclick="clearCache('all')" 
                        class="bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg p-4 text-left transition-colors">
                    <div class="flex items-center">
                        <svg class="h-6 w-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <div>
                            <p class="font-medium text-red-900">Clear All Cache</p>
                            <p class="text-sm text-red-700">Clear all application cache</p>
                        </div>
                    </div>
                </button>
            </div>
            
            <div id="cache-result" class="mt-4 hidden">
                <!-- Cache clear results will be displayed here -->
            </div>
        </div>
    </div>

    <!-- Important Notes -->
    <div class="bg-yellow-50 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">
                    Important Notes
                </h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Maintenance mode will display a maintenance page to all users except administrators</li>
                        <li>Keep your secret key secure and share it only with authorized personnel</li>
                        <li>Consider scheduling maintenance during low-traffic periods</li>
                        <li>Test the maintenance page before enabling it in production</li>
                        <li>Clear cache after making configuration changes</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function clearCache(type) {
    const button = event.target.closest('button');
    const resultDiv = document.getElementById('cache-result');
    
    // Show loading state
    button.disabled = true;
    const originalContent = button.innerHTML;
    button.innerHTML = button.innerHTML.replace(/Clear.*/, 'Clearing...');
    
    resultDiv.classList.remove('hidden');
    resultDiv.innerHTML = '<div class="text-blue-700">Clearing cache...</div>';
    
    // Make API call to clear cache
    fetch('/admin/settings/clear-cache', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            type: type
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = `
                <div class="text-green-700">
                    <div class="font-medium">✓ Cache cleared successfully!</div>
                    <div class="text-sm mt-1">${data.message}</div>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="text-red-700">
                    <div class="font-medium">✗ Failed to clear cache</div>
                    <div class="text-sm mt-1">${data.message}</div>
                </div>
            `;
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `
            <div class="text-red-700">
                <div class="font-medium">✗ Error clearing cache</div>
                <div class="text-sm mt-1">Please try again</div>
            </div>
        `;
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = originalContent;
        
        // Hide result after 5 seconds
        setTimeout(() => {
            resultDiv.classList.add('hidden');
        }, 5000);
    });
}
</script>
@endsection