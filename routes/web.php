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
use App\Http\Controllers\Admin\FinancialManagementController;
use App\Http\Controllers\Admin\SystemSettingsController;
use App\Http\Controllers\Admin\NotificationManagementController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\ContentManagementController;
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
    
    // Financial Management
    Route::prefix('financial')->name('financial.')->group(function () {
        Route::get('/', [FinancialManagementController::class, 'index'])->name('index');
        Route::get('/transactions', [FinancialManagementController::class, 'transactions'])->name('transactions');
        Route::get('/subscriptions', [FinancialManagementController::class, 'subscriptions'])->name('subscriptions');
        Route::get('/plans', [FinancialManagementController::class, 'plans'])->name('plans');
        Route::get('/plans/create', [FinancialManagementController::class, 'createPlan'])->name('plans.create');
        Route::post('/plans', [FinancialManagementController::class, 'storePlan'])->name('plans.store');
        Route::get('/plans/{plan}/edit', [FinancialManagementController::class, 'editPlan'])->name('plans.edit');
        Route::put('/plans/{plan}', [FinancialManagementController::class, 'updatePlan'])->name('plans.update');
        Route::get('/revenue-report', [FinancialManagementController::class, 'revenueReport'])->name('revenue-report');
        Route::post('/payments/{payment}/refund', [FinancialManagementController::class, 'refundPayment'])->name('payments.refund');
    });
    
    // System Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SystemSettingsController::class, 'index'])->name('index');
        Route::get('/general', [SystemSettingsController::class, 'general'])->name('general');
        Route::put('/general', [SystemSettingsController::class, 'updateGeneral'])->name('general.update');
        Route::get('/payment', [SystemSettingsController::class, 'payment'])->name('payment');
        Route::put('/payment', [SystemSettingsController::class, 'updatePayment'])->name('payment.update');
        Route::get('/email', [SystemSettingsController::class, 'email'])->name('email');
        Route::put('/email', [SystemSettingsController::class, 'updateEmail'])->name('email.update');
        Route::get('/security', [SystemSettingsController::class, 'security'])->name('security');
        Route::put('/security', [SystemSettingsController::class, 'updateSecurity'])->name('security.update');
        Route::get('/maintenance', [SystemSettingsController::class, 'maintenance'])->name('maintenance');
        Route::post('/maintenance/toggle', [SystemSettingsController::class, 'toggleMaintenance'])->name('maintenance.toggle');
        Route::get('/cache', [SystemSettingsController::class, 'cache'])->name('cache');
        Route::post('/cache/clear', [SystemSettingsController::class, 'clearCache'])->name('cache.clear');
        Route::post('/cache/optimize', [SystemSettingsController::class, 'optimizeCache'])->name('cache.optimize');
    });
    
    // Notification Management
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationManagementController::class, 'index'])->name('index');
        Route::get('/templates', [NotificationManagementController::class, 'templates'])->name('templates');
        Route::get('/templates/{template}/edit', [NotificationManagementController::class, 'editTemplate'])->name('templates.edit');
        Route::put('/templates/{template}', [NotificationManagementController::class, 'updateTemplate'])->name('templates.update');
        Route::get('/send', [NotificationManagementController::class, 'send'])->name('send');
        Route::post('/send', [NotificationManagementController::class, 'sendNotification'])->name('send.store');
        Route::get('/history', [NotificationManagementController::class, 'history'])->name('history');
        Route::get('/system-alerts', [NotificationManagementController::class, 'systemAlerts'])->name('system-alerts');
        Route::put('/system-alerts', [NotificationManagementController::class, 'updateSystemAlerts'])->name('system-alerts.update');
        Route::get('/preferences', [NotificationManagementController::class, 'preferences'])->name('preferences');
        Route::put('/preferences', [NotificationManagementController::class, 'updatePreferences'])->name('preferences.update');
    });
    
    // Audit & Logs
    Route::prefix('audit')->name('audit.')->group(function () {
        Route::get('/', [AuditController::class, 'index'])->name('index');
        Route::get('/admin-logs', [AuditController::class, 'adminLogs'])->name('admin-logs');
        Route::get('/system-logs', [AuditController::class, 'systemLogs'])->name('system-logs');
        Route::get('/security-logs', [AuditController::class, 'securityLogs'])->name('security-logs');
        Route::get('/user-activity', [AuditController::class, 'userActivity'])->name('user-activity');
        Route::get('/export', [AuditController::class, 'exportLogs'])->name('export');
        Route::post('/clear', [AuditController::class, 'clearLogs'])->name('clear');
        Route::get('/logs/{type}/{id}', [AuditController::class, 'logDetails'])->name('log-details');
    });
    
    // Content Management
    Route::prefix('content')->name('content.')->group(function () {
        Route::get('/', [ContentManagementController::class, 'index'])->name('index');
        
        // Pages
        Route::get('/pages', [ContentManagementController::class, 'pages'])->name('pages');
        Route::get('/pages/create', [ContentManagementController::class, 'createPage'])->name('pages.create');
        Route::post('/pages', [ContentManagementController::class, 'storePage'])->name('pages.store');
        Route::get('/pages/{slug}/edit', [ContentManagementController::class, 'editPage'])->name('pages.edit');
        Route::put('/pages/{slug}', [ContentManagementController::class, 'updatePage'])->name('pages.update');
        Route::delete('/pages/{slug}', [ContentManagementController::class, 'deletePage'])->name('pages.delete');
        
        // Blog Posts
        Route::get('/posts', [ContentManagementController::class, 'posts'])->name('posts');
        Route::get('/posts/create', [ContentManagementController::class, 'createPost'])->name('posts.create');
        Route::post('/posts', [ContentManagementController::class, 'storePost'])->name('posts.store');
        Route::get('/posts/{slug}/edit', [ContentManagementController::class, 'editPost'])->name('posts.edit');
        Route::put('/posts/{slug}', [ContentManagementController::class, 'updatePost'])->name('posts.update');
        Route::delete('/posts/{slug}', [ContentManagementController::class, 'deletePost'])->name('posts.delete');
        
        // FAQs
        Route::get('/faqs', [ContentManagementController::class, 'faqs'])->name('faqs');
        Route::get('/faqs/create', [ContentManagementController::class, 'createFaq'])->name('faqs.create');
        Route::post('/faqs', [ContentManagementController::class, 'storeFaq'])->name('faqs.store');
        Route::get('/faqs/{id}/edit', [ContentManagementController::class, 'editFaq'])->name('faqs.edit');
        Route::put('/faqs/{id}', [ContentManagementController::class, 'updateFaq'])->name('faqs.update');
        Route::delete('/faqs/{id}', [ContentManagementController::class, 'deleteFaq'])->name('faqs.delete');
        
        // Announcements
        Route::get('/announcements', [ContentManagementController::class, 'announcements'])->name('announcements');
        Route::get('/announcements/create', [ContentManagementController::class, 'createAnnouncement'])->name('announcements.create');
        Route::post('/announcements', [ContentManagementController::class, 'storeAnnouncement'])->name('announcements.store');
    });
});

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
