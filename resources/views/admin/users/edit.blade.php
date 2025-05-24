@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit User</h1>
            <p class="text-gray-600">Update user information and permissions</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.users.show', $user) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <span>View User</span>
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

    <!-- Form -->
    <div class="bg-white rounded-lg shadow">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $user->name) }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $user->email) }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Password -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Password</h3>
                <p class="text-sm text-gray-600 mb-4">Leave blank to keep current password</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            New Password
                        </label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm New Password
                        </label>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Roles & Permissions -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Roles & Permissions</h3>
                <div class="space-y-3">
                    @foreach($roles as $role)
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="roles[]" 
                                   value="{{ $role->name }}"
                                   {{ in_array($role->name, old('roles', $user->roles->pluck('name')->toArray())) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">
                                {{ ucfirst($role->name) }}
                                @if($role->name === 'admin')
                                    <span class="text-red-500 text-xs">(Full access)</span>
                                @endif
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('roles')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Account Settings -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Account Settings</h3>
                <div class="space-y-4">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="email_verified" 
                               value="1"
                               {{ old('email_verified', $user->email_verified_at ? '1' : '') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">
                            Mark email as verified
                        </span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="api_enabled" 
                               value="1"
                               {{ old('api_enabled', $user->api_enabled ? '1' : '') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">
                            Enable API access
                        </span>
                    </label>
                </div>
            </div>

            <!-- API Key Management -->
            @if($user->api_key)
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">API Key Management</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Current API Key</p>
                                <p class="text-sm text-gray-600 font-mono">{{ $user->api_key }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <button type="button" 
                                        onclick="copyToClipboard('{{ $user->api_key }}')"
                                        class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-sm">
                                    Copy
                                </button>
                                <button type="button" 
                                        onclick="if(confirm('Generate new API key? This will invalidate the current key.')) { document.getElementById('regenerate-api-form').submit(); }"
                                        class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded text-sm">
                                    Regenerate
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.users.show', $user) }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Hidden form for API key regeneration -->
@if($user->api_key)
    <form id="regenerate-api-form" method="POST" action="{{ route('admin.users.regenerate-api-key', $user) }}" class="hidden">
        @csrf
    </form>
@endif

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // You could show a toast notification here
        alert('API key copied to clipboard!');
    });
}
</script>
@endsection