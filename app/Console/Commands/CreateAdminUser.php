<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = 'admin@geminiprotrader.com';
        $password = 'admin123';

        // Check if admin already exists
        if (User::where('email', $email)->exists()) {
            $this->info('Admin user already exists!');
            return;
        }

        $user = User::create([
            'name' => 'Admin',
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        $user->assignRole('admin');

        $this->info('Admin user created successfully!');
        $this->info('Email: ' . $email);
        $this->info('Password: ' . $password);
    }
}
