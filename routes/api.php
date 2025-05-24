<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SignalApiController;
use App\Http\Controllers\Api\UserApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API routes (no authentication required)
Route::get('/status', function () {
    return response()->json([
        'status' => 'online',
        'version' => '1.0.0',
        'timestamp' => now()->toISOString(),
    ]);
});

// API Documentation
Route::get('/docs', function () {
    return response()->json([
        'message' => 'Gemini Pro Trader API Documentation',
        'version' => '1.0.0',
        'endpoints' => [
            'GET /api/status' => 'Check API status',
            'GET /api/signals' => 'Get trading signals',
            'GET /api/signals/latest' => 'Get latest signals',
            'GET /api/signals/{id}' => 'Get specific signal',
            'POST /api/signals' => 'Create new signal (requires permissions)',
            'PUT /api/signals/{id}' => 'Update signal (requires permissions)',
            'GET /api/signals/stats' => 'Get signal statistics',
            'GET /api/signals/performance' => 'Get performance metrics',
            'GET /api/user/profile' => 'Get user profile',
            'PUT /api/user/profile' => 'Update user profile',
            'GET /api/user/subscription' => 'Get subscription details',
            'GET /api/user/notifications' => 'Get user notifications',
            'POST /api/user/notifications/{id}/read' => 'Mark notification as read',
            'POST /api/user/notifications/read-all' => 'Mark all notifications as read',
            'GET /api/user/api-usage' => 'Get API usage statistics',
            'POST /api/user/regenerate-api-key' => 'Regenerate API key',
            'POST /api/user/toggle-api-access' => 'Enable/disable API access',
        ],
        'authentication' => [
            'method' => 'API Key',
            'header' => 'X-API-Key: your_api_key_here',
            'parameter' => 'api_key=your_api_key_here',
        ],
        'rate_limits' => [
            'requests_per_minute' => 100,
            'burst_limit' => 10,
        ],
    ]);
});

// Signal API Routes
Route::prefix('signals')->group(function () {
    Route::get('/', [SignalApiController::class, 'index']);
    Route::get('/latest', [SignalApiController::class, 'latest']);
    Route::get('/stats', [SignalApiController::class, 'stats']);
    Route::get('/performance', [SignalApiController::class, 'performance']);
    Route::get('/{id}', [SignalApiController::class, 'show']);
    Route::post('/', [SignalApiController::class, 'create']);
    Route::put('/{id}', [SignalApiController::class, 'update']);
});

// User API Routes
Route::prefix('user')->group(function () {
    Route::get('/profile', [UserApiController::class, 'profile']);
    Route::put('/profile', [UserApiController::class, 'updateProfile']);
    Route::get('/subscription', [UserApiController::class, 'subscription']);
    Route::get('/notifications', [UserApiController::class, 'notifications']);
    Route::post('/notifications/{id}/read', [UserApiController::class, 'markNotificationAsRead']);
    Route::post('/notifications/read-all', [UserApiController::class, 'markAllNotificationsAsRead']);
    Route::get('/api-usage', [UserApiController::class, 'apiUsage']);
    Route::post('/regenerate-api-key', [UserApiController::class, 'regenerateApiKey']);
    Route::post('/toggle-api-access', [UserApiController::class, 'toggleApiAccess']);
});

// Webhook endpoints for external integrations
Route::prefix('webhooks')->group(function () {
    // NowPayments webhook
    Route::post('/nowpayments', function (Request $request) {
        // Handle NowPayments IPN notifications
        $payload = $request->all();
        
        // Verify the webhook signature
        $receivedSignature = $request->header('x-nowpayments-sig');
        $expectedSignature = hash_hmac('sha512', $request->getContent(), config('services.nowpayments.ipn_secret'));
        
        if (!hash_equals($expectedSignature, $receivedSignature)) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }
        
        // Process the payment notification
        // This would typically dispatch a job to handle the payment
        
        return response()->json(['status' => 'success']);
    });
    
    // Trading bot webhook
    Route::post('/trading-bot', function (Request $request) {
        // Handle trading bot status updates
        $request->validate([
            'bot_id' => 'required|string',
            'user_id' => 'required|integer',
            'status' => 'required|in:running,stopped,error',
            'message' => 'nullable|string',
            'api_key' => 'required|string',
        ]);
        
        // Verify API key
        $user = \App\Models\User::where('api_key', $request->api_key)->first();
        if (!$user || $user->id !== $request->user_id) {
            return response()->json(['error' => 'Invalid API key'], 401);
        }
        
        // Update bot status
        // This would typically update the bot status in the database
        
        return response()->json(['status' => 'success']);
    });
    
    // External signal provider webhook
    Route::post('/external-signal', function (Request $request) {
        // Handle external signal provider notifications
        $request->validate([
            'provider_key' => 'required|string',
            'pair' => 'required|string',
            'action' => 'required|in:buy,sell',
            'entry_price' => 'required|numeric',
            'take_profit' => 'nullable|numeric',
            'stop_loss' => 'nullable|numeric',
            'analysis' => 'nullable|string',
        ]);
        
        // Verify provider key
        $providerKey = config('services.external_signals.provider_key');
        if ($request->provider_key !== $providerKey) {
            return response()->json(['error' => 'Invalid provider key'], 401);
        }
        
        // Create the signal
        \App\Models\ExpertSignal::create([
            'pair' => $request->pair,
            'action' => $request->action,
            'entry_price' => $request->entry_price,
            'take_profit' => $request->take_profit,
            'stop_loss' => $request->stop_loss,
            'analysis' => $request->analysis,
            'confidence' => 'high',
            'risk_level' => 'medium',
            'status' => 'active',
            'provider_id' => 1, // System provider
        ]);
        
        return response()->json(['status' => 'success']);
    });
});

// Error handling for API routes
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found',
        'timestamp' => now()->toISOString(),
    ], 404);
});