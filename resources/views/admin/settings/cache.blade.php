@extends('layouts.admin')

@section('title', 'Cache Settings')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Cache Settings</h1>
            <p class="text-gray-600">Manage application cache and performance settings</p>
        </div>
    </div>

    <!-- Cache Status Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Configuration Cache</p>
                    <p class="text-lg font-semibold {{ $cacheInfo['config_cached'] ? 'text-green-600' : 'text-red-600' }}">
                        {{ $cacheInfo['config_cached'] ? 'Cached' : 'Not Cached' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Routes Cache</p>
                    <p class="text-lg font-semibold {{ $cacheInfo['routes_cached'] ? 'text-green-600' : 'text-red-600' }}">
                        {{ $cacheInfo['routes_cached'] ? 'Cached' : 'Not Cached' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Views Cache</p>
                    <p class="text-lg font-semibold {{ $cacheInfo['views_cached'] ? 'text-green-600' : 'text-red-600' }}">
                        {{ $cacheInfo['views_cached'] ? 'Cached' : 'Not Cached' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Cache Size</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $cacheInfo['cache_size'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Cache Management Actions -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Cache Management</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Clear Application Cache -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center mb-3">
                        <svg class="h-6 w-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <h4 class="font-medium text-gray-900">Application Cache</h4>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Clear all application cache including data cache, file cache, and Redis cache.</p>
                    <form method="POST" action="{{ route('admin.settings.cache.clear') }}">
                        @csrf
                        <input type="hidden" name="type" value="application">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                            Clear Application Cache
                        </button>
                    </form>
                </div>

                <!-- Clear Configuration Cache -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center mb-3">
                        <svg class="h-6 w-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <h4 class="font-medium text-gray-900">Configuration Cache</h4>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Clear cached configuration files. Useful after changing .env or config files.</p>
                    <form method="POST" action="{{ route('admin.settings.cache.clear') }}">
                        @csrf
                        <input type="hidden" name="type" value="config">
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                            Clear Config Cache
                        </button>
                    </form>
                </div>

                <!-- Clear Route Cache -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center mb-3">
                        <svg class="h-6 w-6 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        <h4 class="font-medium text-gray-900">Route Cache</h4>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Clear cached routes. Required after adding or modifying routes.</p>
                    <form method="POST" action="{{ route('admin.settings.cache.clear') }}">
                        @csrf
                        <input type="hidden" name="type" value="route">
                        <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm">
                            Clear Route Cache
                        </button>
                    </form>
                </div>

                <!-- Clear View Cache -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center mb-3">
                        <svg class="h-6 w-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <h4 class="font-medium text-gray-900">View Cache</h4>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Clear compiled Blade templates. Useful after modifying view files.</p>
                    <form method="POST" action="{{ route('admin.settings.cache.clear') }}">
                        @csrf
                        <input type="hidden" name="type" value="view">
                        <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm">
                            Clear View Cache
                        </button>
                    </form>
                </div>

                <!-- Optimize Application -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center mb-3">
                        <svg class="h-6 w-6 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <h4 class="font-medium text-gray-900">Optimize Application</h4>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Run optimization commands to cache config, routes, and views for production.</p>
                    <form method="POST" action="{{ route('admin.settings.cache.optimize') }}">
                        @csrf
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">
                            Optimize Application
                        </button>
                    </form>
                </div>

                <!-- Clear All Cache -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center mb-3">
                        <svg class="h-6 w-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <h4 class="font-medium text-gray-900">Clear All Cache</h4>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Clear all types of cache including application, config, routes, and views.</p>
                    <form method="POST" action="{{ route('admin.settings.cache.clear') }}">
                        @csrf
                        <input type="hidden" name="type" value="all">
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm">
                            Clear All Cache
                        </button>
                    </form>
                </div>
            </div>
            
            <div id="cache-result" class="mt-6 hidden">
                <!-- Cache operation results will be displayed here -->
            </div>
        </div>
    </div>

    <!-- Cache Configuration -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Cache Configuration</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-2">Current Cache Driver</h4>
                    <p class="text-lg font-semibold text-blue-600">{{ config('cache.default') }}</p>
                    <p class="text-sm text-gray-600 mt-1">The default cache store being used by the application</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-2">Session Driver</h4>
                    <p class="text-lg font-semibold text-green-600">{{ config('session.driver') }}</p>
                    <p class="text-sm text-gray-600 mt-1">The session storage driver being used</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-2">Queue Driver</h4>
                    <p class="text-lg font-semibold text-purple-600">{{ config('queue.default') }}</p>
                    <p class="text-sm text-gray-600 mt-1">The default queue connection for background jobs</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-2">Cache Prefix</h4>
                    <p class="text-lg font-semibold text-yellow-600">{{ config('cache.prefix') ?: 'laravel_cache' }}</p>
                    <p class="text-sm text-gray-600 mt-1">Prefix used for cache keys to avoid conflicts</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Tips -->
    <div class="bg-blue-50 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">
                    Performance Tips
                </h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Use <strong>config:cache</strong> in production to cache configuration files</li>
                        <li>Use <strong>route:cache</strong> in production to cache routes for faster routing</li>
                        <li>Use <strong>view:cache</strong> to pre-compile Blade templates</li>
                        <li>Consider using Redis or Memcached for better cache performance</li>
                        <li>Clear cache after deploying new code or configuration changes</li>
                        <li>Monitor cache hit rates and adjust cache strategies accordingly</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
