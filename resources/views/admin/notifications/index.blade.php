@extends('layouts.admin')

@section('title', 'Notification Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Notification Management</h1>
            <p class="text-gray-600">Manage user notifications and system alerts</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.notifications.send') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                Send Notification
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Sent Today</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['sent_today'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">This Week</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['sent_this_week'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending Alerts</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_alerts'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Templates</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_templates'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Send</h3>
            <p class="text-gray-600 mb-4">Send notifications to users instantly</p>
            <div class="space-y-3">
                <a href="{{ route('admin.notifications.send') }}?type=email" class="block w-full bg-blue-600 text-white text-center py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                    Send Email
                </a>
                <a href="{{ route('admin.notifications.send') }}?type=push" class="block w-full bg-green-600 text-white text-center py-2 px-4 rounded-lg hover:bg-green-700 transition-colors">
                    Send Push Notification
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Templates</h3>
            <p class="text-gray-600 mb-4">Manage notification templates</p>
            <div class="space-y-3">
                <a href="{{ route('admin.notifications.templates') }}" class="block w-full bg-purple-600 text-white text-center py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors">
                    Manage Templates
                </a>
                <a href="{{ route('admin.notifications.templates.create') }}" class="block w-full bg-indigo-600 text-white text-center py-2 px-4 rounded-lg hover:bg-indigo-700 transition-colors">
                    Create Template
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">System Alerts</h3>
            <p class="text-gray-600 mb-4">Configure automated alerts</p>
            <div class="space-y-3">
                <a href="{{ route('admin.notifications.system-alerts') }}" class="block w-full bg-red-600 text-white text-center py-2 px-4 rounded-lg hover:bg-red-700 transition-colors">
                    Alert Settings
                </a>
                <a href="{{ route('admin.notifications.preferences') }}" class="block w-full bg-orange-600 text-white text-center py-2 px-4 rounded-lg hover:bg-orange-700 transition-colors">
                    Preferences
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Notifications -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Recent Notifications</h3>
                <a href="{{ route('admin.notifications.history') }}" class="text-blue-600 hover:text-blue-800">View All</a>
            </div>
        </div>
        <div class="p-6">
            @if(isset($recentNotifications) && $recentNotifications->count() > 0)
                <div class="space-y-4">
                    @foreach($recentNotifications as $notification)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-4">
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    @if($notification->type === 'email')
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6v-6H4v6zM4 5h16v6H4V5z"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $notification->title }}</p>
                                    <p class="text-sm text-gray-600">{{ Str::limit($notification->message, 60) }}</p>
                                    <p class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    @if($notification->type === 'email') bg-blue-100 text-blue-800
                                    @elseif($notification->type === 'push') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($notification->type) }}
                                </span>
                                <span class="text-sm text-gray-500">{{ $notification->recipients_count ?? 1 }} recipients</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No notifications sent</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by sending your first notification.</p>
                    <div class="mt-6">
                        <a href="{{ route('admin.notifications.send') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Send Notification
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection