<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GeminiSignalController;
use App\Http\Controllers\ExpertSignalController;
use App\Http\Controllers\TradingBotController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Gemini RealTime Signal Routes
Route::middleware(['auth'])->prefix('signals')->name('signals.')->group(function () {
    Route::get('/realtime', [GeminiSignalController::class, 'index'])->name('realtime');
    Route::post('/generate', [GeminiSignalController::class, 'generate'])->name('generate');
    Route::post('/analyze', [GeminiSignalController::class, 'analyze'])->name('analyze');
    Route::get('/history', [GeminiSignalController::class, 'history'])->name('history');
});

// Expert Signal Routes
Route::middleware(['auth'])->prefix('expert-signals')->name('expert-signals.')->group(function () {
    Route::get('/', [ExpertSignalController::class, 'index'])->name('index');
    Route::get('/create', [ExpertSignalController::class, 'create'])->name('create');
    Route::post('/', [ExpertSignalController::class, 'store'])->name('store');
    Route::get('/{signal}', [ExpertSignalController::class, 'show'])->name('show');
    Route::get('/{signal}/edit', [ExpertSignalController::class, 'edit'])->name('edit');
    Route::put('/{signal}', [ExpertSignalController::class, 'update'])->name('update');
    Route::delete('/{signal}', [ExpertSignalController::class, 'destroy'])->name('destroy');
});

// Trading Bot Routes
Route::middleware(['auth'])->prefix('bots')->name('bots.')->group(function () {
    Route::get('/', [TradingBotController::class, 'index'])->name('index');
    Route::get('/{bot}', [TradingBotController::class, 'show'])->name('show');
    Route::post('/{bot}/activate', [TradingBotController::class, 'activate'])->name('activate');
    Route::post('/{bot}/deactivate', [TradingBotController::class, 'deactivate'])->name('deactivate');
    Route::post('/{bot}/download', [TradingBotController::class, 'download'])->name('download');
    Route::put('/{bot}/config', [TradingBotController::class, 'updateConfig'])->name('update-config');
});

// Billing & Subscription Routes
Route::middleware(['auth'])->prefix('billing')->name('billing.')->group(function () {
    Route::get('/', [BillingController::class, 'index'])->name('index');
    Route::post('/subscribe', [BillingController::class, 'subscribe'])->name('subscribe');
    Route::get('/payment/{paymentId}', [BillingController::class, 'payment'])->name('payment');
    Route::get('/history', [BillingController::class, 'paymentHistory'])->name('history');
    Route::post('/cancel-subscription', [BillingController::class, 'cancelSubscription'])->name('cancel-subscription');
    Route::get('/success', [BillingController::class, 'success'])->name('success');
    Route::get('/cancel', [BillingController::class, 'cancel'])->name('cancel');
    Route::get('/invoice/{payment}', [BillingController::class, 'downloadInvoice'])->name('download-invoice');
});

// Payment Webhook (no auth middleware)
Route::post('/payments/webhook', [PaymentWebhookController::class, 'handle'])->name('payments.webhook');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [AdminDashboardController::class, 'analytics'])->name('analytics');
    
    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::get('/create', [UserManagementController::class, 'create'])->name('create');
        Route::post('/', [UserManagementController::class, 'store'])->name('store');
        Route::get('/{user}', [UserManagementController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/suspend', [UserManagementController::class, 'suspend'])->name('suspend');
        Route::post('/{user}/unsuspend', [UserManagementController::class, 'unsuspend'])->name('unsuspend');
        Route::post('/{user}/impersonate', [UserManagementController::class, 'impersonate'])->name('impersonate');
        Route::post('/{user}/notification', [UserManagementController::class, 'sendNotification'])->name('send-notification');
    });
    
    // Stop impersonating (available globally)
    Route::post('/stop-impersonating', [UserManagementController::class, 'stopImpersonating'])->name('stop-impersonating');
});

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
