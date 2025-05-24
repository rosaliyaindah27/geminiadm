<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TradingBot;
use App\Models\UserBot;
use App\Models\UserNotification;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TradingBotController extends Controller
{

    public function index()
    {
        $availableBots = TradingBot::active()->get();
        $userBots = auth()->user()->userBots()->with('tradingBot')->get();

        return view('bots.index', compact('availableBots', 'userBots'));
    }

    public function show(TradingBot $bot)
    {
        if (!$bot->is_active) {
            abort(404);
        }

        $userBot = auth()->user()->userBots()
            ->where('trading_bot_id', $bot->id)
            ->first();

        return view('bots.show', compact('bot', 'userBot'));
    }

    public function activate(Request $request, TradingBot $bot)
    {
        $user = auth()->user();

        // Check if user already has this bot
        $existingUserBot = $user->userBots()
            ->where('trading_bot_id', $bot->id)
            ->first();

        if ($existingUserBot) {
            return redirect()
                ->route('bots.show', $bot)
                ->with('error', 'You already have this bot activated.');
        }

        // Check subscription limits (implement based on subscription plan)
        if (!$this->canActivateBot($user, $bot)) {
            return redirect()
                ->route('bots.show', $bot)
                ->with('error', 'Your subscription plan does not allow activating this bot.');
        }

        // Generate license key
        $licenseKey = $bot->generateLicenseKey();

        // Create user bot
        $userBot = UserBot::create([
            'user_id' => $user->id,
            'trading_bot_id' => $bot->id,
            'license_key' => $licenseKey,
            'status' => 'active',
            'activated_at' => now(),
            'bot_config' => $bot->default_config,
        ]);

        // Create notification
        UserNotification::createForUser(
            $user,
            'bot',
            'Trading Bot Activated',
            "Your {$bot->name} trading bot has been successfully activated with license key: {$licenseKey}",
            ['bot_id' => $bot->id, 'license_key' => $licenseKey],
            'medium',
            route('bots.show', $bot)
        );

        return redirect()
            ->route('bots.show', $bot)
            ->with('success', 'Bot activated successfully! Your license key is: ' . $licenseKey);
    }

    public function deactivate(TradingBot $bot)
    {
        $user = auth()->user();
        
        $userBot = $user->userBots()
            ->where('trading_bot_id', $bot->id)
            ->first();

        if (!$userBot) {
            return redirect()
                ->route('bots.index')
                ->with('error', 'Bot not found.');
        }

        $userBot->deactivate();

        return redirect()
            ->route('bots.index')
            ->with('success', 'Bot deactivated successfully.');
    }

    public function download(TradingBot $bot)
    {
        $user = auth()->user();
        
        // Check if user has an active subscription
        if (!$user->hasActiveSubscription()) {
            return redirect()
                ->route('billing.index')
                ->with('error', 'You need an active subscription to download bots.');
        }
        
        // Create or get user bot record
        $userBot = $user->userBots()
            ->where('trading_bot_id', $bot->id)
            ->first();

        if (!$userBot) {
            // Create new user bot record
            $licenseKey = $bot->license_key_prefix . '-' . strtoupper(Str::random(8)) . '-' . strtoupper(Str::random(8));
            
            $userBot = UserBot::create([
                'user_id' => $user->id,
                'trading_bot_id' => $bot->id,
                'license_key' => $licenseKey,
                'status' => 'inactive',
                'activated_at' => now(),
            ]);
        }

        // Create a dummy bot file for download (since we don't have actual files)
        $botContent = $this->generateBotFile($bot, $userBot);
        
        // Create temporary file
        $fileName = $bot->name . '_v' . $bot->version . '.zip';
        $tempPath = storage_path('app/temp/' . $fileName);
        
        // Ensure temp directory exists
        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }
        
        file_put_contents($tempPath, $botContent);

        // Update last activity
        $userBot->updateActivity();

        // Return file download and delete after sending
        return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
    }
    
    private function generateBotFile(TradingBot $bot, UserBot $userBot): string
    {
        $config = [
            'bot_name' => $bot->name,
            'version' => $bot->version,
            'license_key' => $userBot->license_key,
            'bot_type' => $bot->bot_type,
            'supported_exchanges' => $bot->supported_exchanges,
            'supported_pairs' => $bot->supported_pairs,
            'default_config' => $bot->default_config,
            'installation_guide' => $bot->installation_guide,
            'download_date' => now()->toISOString(),
            'user_id' => $userBot->user_id,
        ];
        
        return "# Gemini Pro Trader Bot Package\n\n" .
               "Bot Name: {$bot->name}\n" .
               "Version: {$bot->version}\n" .
               "License Key: {$userBot->license_key}\n\n" .
               "Configuration:\n" .
               json_encode($config, JSON_PRETTY_PRINT) . "\n\n" .
               "Installation Guide:\n" .
               $bot->installation_guide . "\n\n" .
               "Note: This is a demo file. In production, this would contain the actual bot executable files.";
    }

    public function updateConfig(Request $request, TradingBot $bot)
    {
        $user = auth()->user();
        
        $userBot = $user->userBots()
            ->where('trading_bot_id', $bot->id)
            ->first();

        if (!$userBot) {
            return redirect()
                ->route('bots.index')
                ->with('error', 'Bot not found.');
        }

        $validated = $request->validate([
            'config' => 'required|array',
        ]);

        $userBot->update([
            'bot_config' => $validated['config'],
        ]);

        return redirect()
            ->route('bots.show', $bot)
            ->with('success', 'Bot configuration updated successfully.');
    }

    private function canActivateBot($user, TradingBot $bot): bool
    {
        $subscription = $user->activeSubscription;
        
        if (!$subscription) {
            return false;
        }

        $plan = $subscription->subscriptionPlan;
        $activeBots = $user->userBots()->active()->count();

        // Check plan limits
        if ($plan->name === 'Basic Plan' && $activeBots >= 1) {
            return false;
        }

        if ($plan->name === 'Pro Plan' && $activeBots >= 5) {
            return false;
        }

        // Enterprise has unlimited bots
        return true;
    }
}
