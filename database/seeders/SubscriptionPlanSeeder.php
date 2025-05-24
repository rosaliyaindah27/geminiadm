<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic Plan',
                'description' => 'Perfect for beginners who want to explore trading signals and basic bot functionality.',
                'price' => 29.99,
                'currency' => 'USD',
                'billing_period' => 'monthly',
                'features' => [
                    'Access to Gemini RealTime Signal',
                    'View Expert Signals (last 10)',
                    'Download 1 Trading Bot',
                    'Basic support',
                    'Email notifications'
                ],
                'is_active' => true,
                'nowpayments_plan_id' => 'basic_monthly',
            ],
            [
                'name' => 'Pro Plan',
                'description' => 'Advanced features for serious traders who want full access to all signals and multiple bots.',
                'price' => 79.99,
                'currency' => 'USD',
                'billing_period' => 'monthly',
                'features' => [
                    'Access to Gemini RealTime Signal',
                    'Full Expert Signals History',
                    'Download up to 5 Trading Bots',
                    'Priority support',
                    'Real-time notifications',
                    'Advanced analytics',
                    'Custom bot configurations'
                ],
                'is_active' => true,
                'nowpayments_plan_id' => 'pro_monthly',
            ],
            [
                'name' => 'Enterprise Plan',
                'description' => 'Complete solution for professional traders and institutions with unlimited access.',
                'price' => 199.99,
                'currency' => 'USD',
                'billing_period' => 'monthly',
                'features' => [
                    'Access to Gemini RealTime Signal',
                    'Full Expert Signals History',
                    'Unlimited Trading Bots',
                    '24/7 Premium support',
                    'Real-time notifications',
                    'Advanced analytics',
                    'Custom bot configurations',
                    'API access',
                    'White-label options',
                    'Dedicated account manager'
                ],
                'is_active' => true,
                'nowpayments_plan_id' => 'enterprise_monthly',
            ],
            [
                'name' => 'Basic Annual',
                'description' => 'Basic plan with annual billing - save 20%!',
                'price' => 287.90,
                'currency' => 'USD',
                'billing_period' => 'yearly',
                'features' => [
                    'Access to Gemini RealTime Signal',
                    'View Expert Signals (last 10)',
                    'Download 1 Trading Bot',
                    'Basic support',
                    'Email notifications',
                    '20% discount vs monthly'
                ],
                'is_active' => true,
                'nowpayments_plan_id' => 'basic_yearly',
            ],
            [
                'name' => 'Pro Annual',
                'description' => 'Pro plan with annual billing - save 20%!',
                'price' => 767.90,
                'currency' => 'USD',
                'billing_period' => 'yearly',
                'features' => [
                    'Access to Gemini RealTime Signal',
                    'Full Expert Signals History',
                    'Download up to 5 Trading Bots',
                    'Priority support',
                    'Real-time notifications',
                    'Advanced analytics',
                    'Custom bot configurations',
                    '20% discount vs monthly'
                ],
                'is_active' => true,
                'nowpayments_plan_id' => 'pro_yearly',
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::firstOrCreate(
                ['nowpayments_plan_id' => $plan['nowpayments_plan_id']],
                $plan
            );
        }
    }
}
