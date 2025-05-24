<?php

namespace App\Helpers;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

class SettingsHelper
{
    /**
     * Get a system setting value
     */
    public static function get($key, $default = null)
    {
        return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            return SystemSetting::get($key, $default);
        });
    }

    /**
     * Set a system setting value
     */
    public static function set($key, $value, $type = 'string', $description = null, $group = 'general')
    {
        Cache::forget("setting.{$key}");
        Cache::forget("settings.group.{$group}");
        
        return SystemSetting::set($key, $value, $type, $description, $group);
    }

    /**
     * Get all settings in a group
     */
    public static function getGroup($group)
    {
        return Cache::remember("settings.group.{$group}", 3600, function () use ($group) {
            return SystemSetting::getGroup($group);
        });
    }

    /**
     * Clear settings cache
     */
    public static function clearCache()
    {
        $keys = Cache::get('setting_keys', []);
        
        foreach ($keys as $key) {
            Cache::forget("setting.{$key}");
        }
        
        // Clear group caches
        $groups = ['general', 'email', 'payment', 'security', 'api', 'trading', 'notifications'];
        foreach ($groups as $group) {
            Cache::forget("settings.group.{$group}");
        }
        
        Cache::forget('setting_keys');
    }

    /**
     * Get site configuration
     */
    public static function getSiteConfig()
    {
        return [
            'name' => self::get('site_name', 'Gemini Pro Trader'),
            'description' => self::get('site_description', 'Professional Trading Solutions'),
            'logo' => self::get('site_logo', '/images/logo.png'),
            'timezone' => self::get('default_timezone', 'UTC'),
            'maintenance_mode' => self::get('maintenance_mode', false),
        ];
    }

    /**
     * Get email configuration
     */
    public static function getEmailConfig()
    {
        return [
            'driver' => self::get('mail_driver', 'smtp'),
            'host' => self::get('mail_host', 'localhost'),
            'port' => self::get('mail_port', 587),
            'username' => self::get('mail_username', ''),
            'encryption' => self::get('mail_encryption', 'tls'),
            'from_address' => self::get('mail_from_address', 'noreply@example.com'),
            'from_name' => self::get('mail_from_name', 'Gemini Pro Trader'),
        ];
    }

    /**
     * Get payment configuration
     */
    public static function getPaymentConfig()
    {
        return [
            'nowpayments_api_key' => self::get('nowpayments_api_key', ''),
            'nowpayments_ipn_secret' => self::get('nowpayments_ipn_secret', ''),
            'nowpayments_public_key' => self::get('nowpayments_public_key', ''),
            'currencies' => self::get('payment_currencies', ['BTC', 'ETH', 'USDT']),
            'default_currency' => self::get('default_currency', 'USD'),
        ];
    }

    /**
     * Get security configuration
     */
    public static function getSecurityConfig()
    {
        return [
            'session_lifetime' => self::get('session_lifetime', 120),
            'password_min_length' => self::get('password_min_length', 8),
            'require_email_verification' => self::get('require_email_verification', true),
            'enable_2fa' => self::get('enable_2fa', true),
            'max_login_attempts' => self::get('max_login_attempts', 5),
            'lockout_duration' => self::get('lockout_duration', 15),
        ];
    }

    /**
     * Get API configuration
     */
    public static function getApiConfig()
    {
        return [
            'rate_limit' => self::get('api_rate_limit', 100),
            'enabled' => self::get('api_enabled', true),
            'version' => self::get('api_version', '1.0.0'),
        ];
    }

    /**
     * Get trading configuration
     */
    public static function getTradingConfig()
    {
        return [
            'max_signals_per_day' => self::get('max_signals_per_day', 50),
            'signal_retention_days' => self::get('signal_retention_days', 90),
            'default_risk_level' => self::get('default_risk_level', 'medium'),
            'supported_exchanges' => self::get('supported_exchanges', ['Binance', 'Coinbase']),
        ];
    }

    /**
     * Get notification configuration
     */
    public static function getNotificationConfig()
    {
        return [
            'enable_email' => self::get('enable_email_notifications', true),
            'enable_push' => self::get('enable_push_notifications', true),
            'queue_driver' => self::get('notification_queue_driver', 'database'),
        ];
    }

    /**
     * Check if maintenance mode is enabled
     */
    public static function isMaintenanceMode()
    {
        return self::get('maintenance_mode', false);
    }

    /**
     * Check if feature is enabled
     */
    public static function isFeatureEnabled($feature)
    {
        return self::get("enable_{$feature}", false);
    }

    /**
     * Get all settings for admin panel
     */
    public static function getAllForAdmin()
    {
        return SystemSetting::getAllSettings();
    }

    /**
     * Bulk update settings
     */
    public static function bulkUpdate($settings)
    {
        foreach ($settings as $key => $value) {
            $setting = SystemSetting::where('key', $key)->first();
            if ($setting) {
                $setting->update(['value' => $value]);
                Cache::forget("setting.{$key}");
            }
        }
        
        self::clearCache();
    }
}