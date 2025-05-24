<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class ApiKeyAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key') ?? $request->get('api_key');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key is required',
                'error' => 'Missing API key in request headers or parameters',
            ], 401);
        }

        $user = User::where('api_key', $apiKey)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key',
                'error' => 'The provided API key is not valid',
            ], 401);
        }

        if (!$user->api_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'API access disabled',
                'error' => 'API access has been disabled for this account',
            ], 403);
        }

        // Set the authenticated user
        auth()->setUser($user);

        // Log API usage (optional)
        $this->logApiUsage($user, $request);

        return $next($request);
    }

    private function logApiUsage(User $user, Request $request)
    {
        // Log API usage for analytics and rate limiting
        // This could be stored in a separate table or cache
        $logData = [
            'user_id' => $user->id,
            'endpoint' => $request->path(),
            'method' => $request->method(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
        ];

        // Store in cache for rate limiting
        $cacheKey = 'api_usage:' . $user->id . ':' . now()->format('Y-m-d-H');
        $currentUsage = cache()->get($cacheKey, 0);
        cache()->put($cacheKey, $currentUsage + 1, now()->addHour());

        // You could also store in database for detailed analytics
        // ApiUsageLog::create($logData);
    }
}