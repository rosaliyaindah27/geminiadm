<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'site_name',
                'value' => 'Gemini Pro Trader',
                'type' => 'string',
                'description' => 'Website name displayed in headers and emails',
                'group' => 'general',
            ],
            [
                'key' => 'site_description',
                'value' => 'Professional Trading Solutions for Cryptocurrency Markets',
                'type' => 'string',
                'description' => 'Website description for SEO and marketing',
                'group' => 'general',
            ],
            [
                'key' => 'site_logo',
                'value' => '/images/logo.png',
                'type' => 'string',
                'description' => 'Path to site logo image',
                'group' => 'general',
            ],
            [
                'key' => 'maintenance_mode',
                'value' => false,
                'type' => 'boolean',
                'description' => 'Enable maintenance mode to restrict access',
                'group' => 'general',
            ],
            [
                'key' => 'default_timezone',
                'value' => 'UTC',
                'type' => 'string',
                'description' => 'Default timezone for the application',
                'group' => 'general',
            ],

            // Email Settings
            [
                'key' => 'mail_driver',
                'value' => 'smtp',
                'type' => 'string',
                'description' => 'Email driver (smtp, sendmail, mailgun, etc.)',
                'group' => 'email',
            ],
            [
                'key' => 'mail_host',
                'value' => 'mail.geminiprotrader.com',
                'type' => 'string',
                'description' => 'SMTP server hostname',
                'group' => 'email',
            ],
            [
                'key' => 'mail_port',
                'value' => 465,
                'type' => 'integer',
                'description' => 'SMTP server port',
                'group' => 'email',
            ],
            [
                'key' => 'mail_username',
                'value' => 'noreply@geminiprotrader.com',
                'type' => 'string',
                'description' => 'SMTP username',
                'group' => 'email',
            ],
            [
                'key' => 'mail_encryption',
                'value' => 'ssl',
                'type' => 'string',
                'description' => 'Email encryption (tls, ssl, null)',
                'group' => 'email',
            ],
            [
                'key' => 'mail_from_address',
                'value' => 'noreply@geminiprotrader.com',
                'type' => 'string',
                'description' => 'Default from email address',
                'group' => 'email',
            ],
            [
                'key' => 'mail_from_name',
                'value' => 'Gemini Pro Trader',
                'type' => 'string',
                'description' => 'Default from name',
                'group' => 'email',
            ],

            // Payment Settings
            [
                'key' => 'nowpayments_api_key',
                'value' => 'A2BYB64-8DFMH2B-PESRT93-TYZK4GK',
                'type' => 'string',
                'description' => 'NowPayments API key',
                'group' => 'payment',
            ],
            [
                'key' => 'nowpayments_ipn_secret',
                'value' => 'FPRQamdSK2vPtSHDUGbtcs5GHSCeuW4d',
                'type' => 'string',
                'description' => 'NowPayments IPN secret key',
                'group' => 'payment',
            ],
            [
                'key' => 'nowpayments_public_key',
                'value' => '0d39caa5-6042-4284-a614-451f3958ec8b',
                'type' => 'string',
                'description' => 'NowPayments public key',
                'group' => 'payment',
            ],
            [
                'key' => 'payment_currencies',
                'value' => ['BTC', 'ETH', 'USDT', 'LTC', 'BCH'],
                'type' => 'array',
                'description' => 'Accepted payment currencies',
                'group' => 'payment',
            ],
            [
                'key' => 'default_currency',
                'value' => 'USD',
                'type' => 'string',
                'description' => 'Default currency for pricing',
                'group' => 'payment',
            ],

            // Security Settings
            [
                'key' => 'session_lifetime',
                'value' => 120,
                'type' => 'integer',
                'description' => 'Session lifetime in minutes',
                'group' => 'security',
            ],
            [
                'key' => 'password_min_length',
                'value' => 8,
                'type' => 'integer',
                'description' => 'Minimum password length',
                'group' => 'security',
            ],
            [
                'key' => 'require_email_verification',
                'value' => true,
                'type' => 'boolean',
                'description' => 'Require email verification for new accounts',
                'group' => 'security',
            ],
            [
                'key' => 'enable_2fa',
                'value' => true,
                'type' => 'boolean',
                'description' => 'Enable two-factor authentication',
                'group' => 'security',
            ],
            [
                'key' => 'max_login_attempts',
                'value' => 5,
                'type' => 'integer',
                'description' => 'Maximum login attempts before lockout',
                'group' => 'security',
            ],
            [
                'key' => 'lockout_duration',
                'value' => 15,
                'type' => 'integer',
                'description' => 'Account lockout duration in minutes',
                'group' => 'security',
            ],

            // API Settings
            [
                'key' => 'api_rate_limit',
                'value' => 100,
                'type' => 'integer',
                'description' => 'API rate limit per minute',
                'group' => 'api',
            ],
            [
                'key' => 'api_enabled',
                'value' => true,
                'type' => 'boolean',
                'description' => 'Enable API access',
                'group' => 'api',
            ],
            [
                'key' => 'api_version',
                'value' => '1.0.0',
                'type' => 'string',
                'description' => 'Current API version',
                'group' => 'api',
            ],

            // Trading Settings
            [
                'key' => 'max_signals_per_day',
                'value' => 50,
                'type' => 'integer',
                'description' => 'Maximum signals to generate per day',
                'group' => 'trading',
            ],
            [
                'key' => 'signal_retention_days',
                'value' => 90,
                'type' => 'integer',
                'description' => 'Days to keep signal history',
                'group' => 'trading',
            ],
            [
                'key' => 'default_risk_level',
                'value' => 'medium',
                'type' => 'string',
                'description' => 'Default risk level for new signals',
                'group' => 'trading',
            ],
            [
                'key' => 'supported_exchanges',
                'value' => ['Binance', 'Coinbase', 'Kraken', 'Bitfinex', 'KuCoin'],
                'type' => 'array',
                'description' => 'List of supported exchanges',
                'group' => 'trading',
            ],

            // Notification Settings
            [
                'key' => 'enable_email_notifications',
                'value' => true,
                'type' => 'boolean',
                'description' => 'Enable email notifications',
                'group' => 'notifications',
            ],
            [
                'key' => 'enable_push_notifications',
                'value' => true,
                'type' => 'boolean',
                'description' => 'Enable push notifications',
                'group' => 'notifications',
            ],
            [
                'key' => 'notification_queue_driver',
                'value' => 'database',
                'type' => 'string',
                'description' => 'Queue driver for notifications',
                'group' => 'notifications',
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}