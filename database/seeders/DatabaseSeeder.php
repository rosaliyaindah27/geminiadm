<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SubscriptionPlanSeeder::class,
            TradingBotSeeder::class,
        ]);

        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@geminiprotrader.com',
        ]);
        $admin->assignRole('admin');

        // Create signal provider user
        $provider = User::factory()->create([
            'name' => 'Signal Provider',
            'email' => 'provider@geminiprotrader.com',
        ]);
        $provider->assignRole('signal_provider');

        // Create test user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $user->assignRole('user');
    }
}
