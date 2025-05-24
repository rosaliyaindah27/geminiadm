<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TradingBot;

class TradingBotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bots = [
            [
                'name' => 'Gemini Scalper Pro',
                'description' => 'Advanced scalping bot designed for high-frequency trading with sophisticated risk management. Perfect for volatile markets.',
                'version' => '2.1.0',
                'bot_type' => 'scalping',
                'file_path' => 'bots/gemini-scalper-pro-v2.1.0.zip',
                'file_hash' => hash('sha256', 'gemini-scalper-pro-v2.1.0'),
                'min_balance' => 500.00,
                'supported_exchanges' => ['Binance', 'Coinbase Pro', 'Kraken', 'Bitfinex'],
                'supported_pairs' => ['BTC/USDT', 'ETH/USDT', 'BNB/USDT', 'ADA/USDT', 'DOT/USDT'],
                'default_config' => [
                    'risk_percentage' => 2.0,
                    'max_trades_per_day' => 50,
                    'stop_loss_percentage' => 1.5,
                    'take_profit_percentage' => 3.0,
                    'timeframe' => '1m',
                    'indicators' => ['RSI', 'MACD', 'Bollinger Bands'],
                    'features' => [
                        'Real-time market analysis',
                        'Advanced risk management',
                        'Multi-timeframe analysis',
                        'Stop-loss and take-profit automation',
                        'Backtesting capabilities',
                        'Portfolio management',
                        'Email/SMS notifications'
                    ],
                    'requirements' => [
                        'Python 3.8+',
                        'Minimum 4GB RAM',
                        'Stable internet connection',
                        'Exchange API keys'
                    ]
                ],
                'is_active' => true,
                'requires_license' => true,
                'license_key_prefix' => 'GSP',
                'installation_guide' => 'Download the bot file, extract it, configure your API keys in config.json, and run python main.py',
            ],
            [
                'name' => 'Gemini Grid Trader',
                'description' => 'Grid trading bot that profits from market volatility by placing buy and sell orders at predetermined intervals.',
                'version' => '1.5.2',
                'bot_type' => 'grid',
                'file_path' => 'bots/gemini-grid-trader-v1.5.2.zip',
                'file_hash' => hash('sha256', 'gemini-grid-trader-v1.5.2'),
                'min_balance' => 300.00,
                'supported_exchanges' => ['Binance', 'Coinbase Pro', 'KuCoin'],
                'supported_pairs' => ['BTC/USDT', 'ETH/USDT', 'LTC/USDT', 'XRP/USDT'],
                'default_config' => [
                    'grid_size' => 10,
                    'grid_spacing' => 0.5,
                    'base_order_size' => 100,
                    'safety_orders' => 5,
                    'max_active_deals' => 3,
                    'features' => [
                        'Grid trading strategy',
                        'Customizable grid parameters',
                        'Automatic rebalancing',
                        'Market volatility analysis',
                        'Profit tracking',
                        'Risk management tools'
                    ],
                    'requirements' => [
                        'Python 3.7+',
                        'Minimum 2GB RAM',
                        'Stable internet connection',
                        'Exchange API keys'
                    ]
                ],
                'is_active' => true,
                'requires_license' => true,
                'license_key_prefix' => 'GGT',
                'installation_guide' => 'Extract the bot files, configure grid parameters in settings.json, add your exchange API credentials, and execute start.py',
            ],
            [
                'name' => 'Gemini DCA Master',
                'description' => 'Dollar Cost Averaging bot that automatically invests fixed amounts at regular intervals, perfect for long-term investors.',
                'version' => '3.0.1',
                'bot_type' => 'dca',
                'file_path' => 'bots/gemini-dca-master-v3.0.1.zip',
                'file_hash' => hash('sha256', 'gemini-dca-master-v3.0.1'),
                'min_balance' => 100.00,
                'supported_exchanges' => ['Binance', 'Coinbase Pro', 'Kraken'],
                'supported_pairs' => ['BTC/USDT', 'ETH/USDT', 'ADA/USDT', 'DOT/USDT', 'LINK/USDT'],
                'default_config' => [
                    'investment_amount' => 100,
                    'frequency' => 'weekly',
                    'assets' => ['BTC', 'ETH'],
                    'allocation' => ['BTC' => 60, 'ETH' => 40],
                    'rebalance_threshold' => 5,
                    'features' => [
                        'Dollar Cost Averaging strategy',
                        'Flexible scheduling',
                        'Portfolio diversification',
                        'Market condition analysis',
                        'Automated rebalancing',
                        'Performance tracking'
                    ],
                    'requirements' => [
                        'Python 3.6+',
                        'Minimum 1GB RAM',
                        'Stable internet connection',
                        'Exchange API keys'
                    ]
                ],
                'is_active' => true,
                'requires_license' => true,
                'license_key_prefix' => 'GDM',
                'installation_guide' => 'Unzip the package, set up your investment schedule in config.yaml, configure exchange connections, and run scheduler.py',
            ],
            [
                'name' => 'Gemini Arbitrage Hunter',
                'description' => 'Advanced arbitrage bot that identifies and exploits price differences across multiple exchanges for risk-free profits.',
                'version' => '1.8.0',
                'bot_type' => 'arbitrage',
                'file_path' => 'bots/gemini-arbitrage-hunter-v1.8.0.zip',
                'file_hash' => hash('sha256', 'gemini-arbitrage-hunter-v1.8.0'),
                'min_balance' => 1000.00,
                'supported_exchanges' => ['Binance', 'Coinbase Pro', 'Kraken', 'Bitfinex', 'KuCoin'],
                'supported_pairs' => ['BTC/USDT', 'ETH/USDT', 'LTC/USDT'],
                'default_config' => [
                    'min_profit_threshold' => 0.5,
                    'max_trade_amount' => 1000,
                    'exchanges' => ['binance', 'coinbase'],
                    'monitoring_interval' => 5,
                    'slippage_tolerance' => 0.1,
                    'features' => [
                        'Multi-exchange arbitrage',
                        'Real-time price monitoring',
                        'Automatic execution',
                        'Fee calculation',
                        'Profit optimization',
                        'Risk assessment'
                    ],
                    'requirements' => [
                        'Python 3.8+',
                        'Minimum 8GB RAM',
                        'High-speed internet connection',
                        'Multiple exchange API keys',
                        'Sufficient balance on all exchanges'
                    ]
                ],
                'is_active' => true,
                'requires_license' => true,
                'license_key_prefix' => 'GAH',
                'installation_guide' => 'Extract files, configure multiple exchange API keys in exchanges.json, set arbitrage parameters, and launch arbitrage.py',
            ],
            [
                'name' => 'Gemini Trend Follower',
                'description' => 'Trend-following bot that identifies and trades with market trends using advanced technical analysis.',
                'version' => '2.3.1',
                'bot_type' => 'swing',
                'file_path' => 'bots/gemini-trend-follower-v2.3.1.zip',
                'file_hash' => hash('sha256', 'gemini-trend-follower-v2.3.1'),
                'min_balance' => 750.00,
                'supported_exchanges' => ['Binance', 'Coinbase Pro', 'Bitfinex'],
                'supported_pairs' => ['BTC/USDT', 'ETH/USDT', 'BNB/USDT', 'ADA/USDT'],
                'default_config' => [
                    'trend_timeframes' => ['4h', '1d'],
                    'entry_signals' => ['EMA_crossover', 'RSI_oversold'],
                    'position_size' => 5,
                    'trailing_stop' => 2.0,
                    'max_drawdown' => 10,
                    'features' => [
                        'Trend identification',
                        'Multiple timeframe analysis',
                        'Dynamic position sizing',
                        'Trailing stop-loss',
                        'Market sentiment analysis',
                        'Backtesting engine'
                    ],
                    'requirements' => [
                        'Python 3.7+',
                        'Minimum 4GB RAM',
                        'Stable internet connection',
                        'Exchange API keys'
                    ]
                ],
                'is_active' => true,
                'requires_license' => true,
                'license_key_prefix' => 'GTF',
                'installation_guide' => 'Unpack the archive, configure trend analysis parameters in trend_config.json, set up API connections, and execute trend_bot.py',
            ],
        ];

        foreach ($bots as $bot) {
            TradingBot::firstOrCreate(
                ['name' => $bot['name']],
                $bot
            );
        }
    }
}
