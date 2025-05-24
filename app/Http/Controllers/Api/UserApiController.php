<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserApiController extends ApiController
{
    public function profile(Request $request): JsonResponse
    {
        if (!$this->validateApiKey($request)) {
            return $this->errorResponse('Invalid API key', 401);
        }

        if ($this->rateLimitExceeded($request)) {
            return $this->errorResponse('Rate limit exceeded', 429);
        }

        $user = auth()->user();
        
        $profile = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'created_at' => $user->created_at,
            'subscription' => $user->subscription ? [
                'plan' => $user->subscription->plan->name,
                'status' => $user->subscription->status,
                'expires_at' => $user->subscription->expires_at,
                'next_billing_date' => $user->subscription->next_billing_date,
            ] : null,
            'api_enabled' => $user->api_enabled,
            'api_requests_today' => $this->getApiRequestsCount($user),
        ];

        return $this->successResponse($profile, 'Profile retrieved successfully');
    }

    public function updateProfile(Request $request): JsonResponse
    {
        if (!$this->validateApiKey($request)) {
            return $this->errorResponse('Invalid API key', 401);
        }

        if ($this->rateLimitExceeded($request)) {
            return $this->errorResponse('Rate limit exceeded', 429);
        }

        $user = auth()->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'current_password' => 'required_with:password',
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        // Verify current password if changing password
        if ($request->has('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return $this->errorResponse('Current password is incorrect', 400);
            }
        }

        $updateData = $request->only(['name', 'email']);
        
        if ($request->has('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return $this->successResponse($user->fresh(), 'Profile updated successfully');
    }

    public function subscription(Request $request): JsonResponse
    {
        if (!$this->validateApiKey($request)) {
            return $this->errorResponse('Invalid API key', 401);
        }

        if ($this->rateLimitExceeded($request)) {
            return $this->errorResponse('Rate limit exceeded', 429);
        }

        $user = auth()->user();
        $subscription = $user->subscription;

        if (!$subscription) {
            return $this->errorResponse('No active subscription found', 404);
        }

        $subscriptionData = [
            'id' => $subscription->id,
            'plan' => [
                'id' => $subscription->plan->id,
                'name' => $subscription->plan->name,
                'description' => $subscription->plan->description,
                'price' => $subscription->plan->price,
                'billing_cycle' => $subscription->plan->billing_cycle,
                'features' => $subscription->plan->features,
            ],
            'status' => $subscription->status,
            'started_at' => $subscription->started_at,
            'expires_at' => $subscription->expires_at,
            'next_billing_date' => $subscription->next_billing_date,
            'auto_renew' => $subscription->auto_renew,
            'created_at' => $subscription->created_at,
        ];

        return $this->successResponse($subscriptionData, 'Subscription details retrieved successfully');
    }

    public function notifications(Request $request): JsonResponse
    {
        if (!$this->validateApiKey($request)) {
            return $this->errorResponse('Invalid API key', 401);
        }

        if ($this->rateLimitExceeded($request)) {
            return $this->errorResponse('Rate limit exceeded', 429);
        }

        $user = auth()->user();
        $perPage = min($request->get('per_page', 20), 100);

        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return $this->paginatedResponse($notifications, 'Notifications retrieved successfully');
    }

    public function markNotificationAsRead(Request $request, $notificationId): JsonResponse
    {
        if (!$this->validateApiKey($request)) {
            return $this->errorResponse('Invalid API key', 401);
        }

        if ($this->rateLimitExceeded($request)) {
            return $this->errorResponse('Rate limit exceeded', 429);
        }

        $user = auth()->user();
        $notification = $user->notifications()->find($notificationId);

        if (!$notification) {
            return $this->errorResponse('Notification not found', 404);
        }

        $notification->update(['read_at' => now()]);

        return $this->successResponse(null, 'Notification marked as read');
    }

    public function markAllNotificationsAsRead(Request $request): JsonResponse
    {
        if (!$this->validateApiKey($request)) {
            return $this->errorResponse('Invalid API key', 401);
        }

        if ($this->rateLimitExceeded($request)) {
            return $this->errorResponse('Rate limit exceeded', 429);
        }

        $user = auth()->user();
        
        $user->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return $this->successResponse(null, 'All notifications marked as read');
    }

    public function apiUsage(Request $request): JsonResponse
    {
        if (!$this->validateApiKey($request)) {
            return $this->errorResponse('Invalid API key', 401);
        }

        if ($this->rateLimitExceeded($request)) {
            return $this->errorResponse('Rate limit exceeded', 429);
        }

        $user = auth()->user();
        $period = $request->get('period', 30); // days

        $usage = [
            'api_key_created' => $user->api_key_created_at,
            'api_enabled' => $user->api_enabled,
            'requests_today' => $this->getApiRequestsCount($user, 1),
            'requests_this_week' => $this->getApiRequestsCount($user, 7),
            'requests_this_month' => $this->getApiRequestsCount($user, 30),
            'requests_period' => $this->getApiRequestsCount($user, $period),
            'rate_limit' => [
                'max_requests_per_minute' => 100,
                'current_usage' => $this->getCurrentRateLimit($user),
            ],
            'endpoints_used' => $this->getEndpointsUsage($user, $period),
        ];

        return $this->successResponse($usage, 'API usage statistics retrieved successfully');
    }

    public function regenerateApiKey(Request $request): JsonResponse
    {
        if (!$this->validateApiKey($request)) {
            return $this->errorResponse('Invalid API key', 401);
        }

        if ($this->rateLimitExceeded($request)) {
            return $this->errorResponse('Rate limit exceeded', 429);
        }

        $user = auth()->user();
        
        $newApiKey = $this->generateApiKey();
        
        $user->update([
            'api_key' => $newApiKey,
            'api_key_created_at' => now(),
        ]);

        return $this->successResponse([
            'api_key' => $newApiKey,
            'created_at' => now()->toISOString(),
        ], 'API key regenerated successfully');
    }

    public function toggleApiAccess(Request $request): JsonResponse
    {
        if (!$this->validateApiKey($request)) {
            return $this->errorResponse('Invalid API key', 401);
        }

        if ($this->rateLimitExceeded($request)) {
            return $this->errorResponse('Rate limit exceeded', 429);
        }

        $user = auth()->user();
        
        $user->update([
            'api_enabled' => !$user->api_enabled,
        ]);

        $status = $user->api_enabled ? 'enabled' : 'disabled';

        return $this->successResponse([
            'api_enabled' => $user->api_enabled,
        ], "API access {$status} successfully");
    }

    private function getApiRequestsCount(User $user, int $days = 1): int
    {
        // This would typically query an API usage log table
        // For now, return a mock value
        return rand(0, 50 * $days);
    }

    private function getCurrentRateLimit(User $user): int
    {
        $key = 'api_rate_limit:' . request()->ip();
        return cache()->get($key, 0);
    }

    private function getEndpointsUsage(User $user, int $days): array
    {
        // This would typically query an API usage log table
        // For now, return mock data
        return [
            '/api/signals' => rand(10, 100),
            '/api/signals/latest' => rand(5, 50),
            '/api/user/profile' => rand(1, 10),
            '/api/user/notifications' => rand(2, 20),
        ];
    }

    private function generateApiKey(): string
    {
        return 'gpt_' . bin2hex(random_bytes(32));
    }
}