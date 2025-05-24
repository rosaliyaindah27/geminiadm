@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">User Details</h1>
            <p class="text-gray-600">{{ $user->name }} ({{ $user->email }})</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.users.edit', $user) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>Edit User</span>
            </a>
            <a href="{{ route('admin.users.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Back to Users</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Full Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $user->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Email Address</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Email Verified</label>
                        <p class="mt-1">
                            @if($user->email_verified_at)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Verified on {{ $user->email_verified_at->format('M d, Y') }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Not Verified
                                </span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Member Since</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Roles & Permissions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Roles & Permissions</h3>
                <div class="flex flex-wrap gap-2">
                    @forelse($user->roles as $role)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                            {{ $role->name === 'admin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst($role->name) }}
                        </span>
                    @empty
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            User (Default)
                        </span>
                    @endforelse
                </div>
            </div>

            <!-- API Access -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">API Access</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">API Status</label>
                        <p class="mt-1">
                            @if($user->api_enabled)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Enabled
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Disabled
                                </span>
                            @endif
                        </p>
                    </div>
                    @if($user->api_key)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">API Key</label>
                            <p class="mt-1 text-sm text-gray-900 font-mono">{{ $user->api_key }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Subscription History -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Subscription History</h3>
                @if($user->subscriptions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Started</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expires</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($user->subscriptions as $subscription)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $subscription->subscriptionPlan->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($subscription->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $subscription->starts_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $subscription->ends_at ? $subscription->ends_at->format('M d, Y') : 'Never' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500">No subscription history found.</p>
                @endif
            </div>

            <!-- Trading Bots -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Trading Bots</h3>
                @if($user->userBots->count() > 0)
                    <div class="space-y-3">
                        @foreach($user->userBots as $userBot)
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">{{ $userBot->tradingBot->name }}</h4>
                                    <p class="text-sm text-gray-500">{{ $userBot->tradingBot->description }}</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $userBot->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $userBot->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No trading bots activated.</p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Stats</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Total Payments</label>
                        <p class="mt-1 text-2xl font-bold text-gray-900">${{ number_format($user->payments->sum('amount'), 2) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Active Bots</label>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $user->userBots->where('is_active', true)->count() }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Notifications</label>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $user->userNotifications->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
                @if($user->userNotifications->count() > 0)
                    <div class="space-y-3">
                        @foreach($user->userNotifications->take(5) as $notification)
                            <div class="text-sm">
                                <p class="text-gray-900">{{ $notification->title }}</p>
                                <p class="text-gray-500 text-xs">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No recent activity.</p>
                @endif
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                <div class="space-y-3">
                    @if(!$user->email_verified_at)
                        <form method="POST" action="{{ route('admin.users.verify-email', $user) }}">
                            @csrf
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                                Verify Email
                            </button>
                        </form>
                    @endif
                    
                    @if(!$user->api_key && $user->api_enabled)
                        <form method="POST" action="{{ route('admin.users.generate-api-key', $user) }}">
                            @csrf
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                                Generate API Key
                            </button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('admin.users.reset-password', $user) }}">
                        @csrf
                        <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm">
                            Reset Password
                        </button>
                    </form>

                    @if(!$user->is_admin)
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" 
                              onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm">
                                Delete User
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection