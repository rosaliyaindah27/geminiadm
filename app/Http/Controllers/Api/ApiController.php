<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    protected function successResponse($data = null, $message = 'Success', $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toISOString(),
        ], $code);
    }

    protected function errorResponse($message = 'Error', $code = 400, $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => now()->toISOString(),
        ], $code);
    }

    protected function paginatedResponse($data, $message = 'Success'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
            ],
            'timestamp' => now()->toISOString(),
        ]);
    }

    protected function validateApiKey(Request $request): bool
    {
        $apiKey = $request->header('X-API-Key') ?? $request->get('api_key');
        
        if (!$apiKey) {
            return false;
        }

        // Validate API key against user's API key
        $user = \App\Models\User::where('api_key', $apiKey)->first();
        
        if (!$user || !$user->api_enabled) {
            return false;
        }

        // Set authenticated user for the request
        auth()->setUser($user);
        
        return true;
    }

    protected function rateLimitExceeded(Request $request): bool
    {
        $key = 'api_rate_limit:' . ($request->ip() ?? 'unknown');
        $maxAttempts = 100; // requests per minute
        $decayMinutes = 1;

        $attempts = cache()->get($key, 0);
        
        if ($attempts >= $maxAttempts) {
            return true;
        }

        cache()->put($key, $attempts + 1, now()->addMinutes($decayMinutes));
        
        return false;
    }
}