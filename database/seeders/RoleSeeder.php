<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // User permissions
            'view dashboard',
            'use realtime signals',
            'view expert signals',
            'download bots',
            'manage subscription',
            
            // Signal provider permissions
            'create expert signals',
            'edit own signals',
            
            // Admin permissions
            'admin dashboard',
            'manage users',
            'manage subscriptions',
            'manage payments',
            'manage signals',
            'manage bots',
            'manage content',
            'view reports',
            'system settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $signalProviderRole = Role::firstOrCreate(['name' => 'signal_provider']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Assign permissions to roles
        $adminRole->givePermissionTo(Permission::all());
        
        $signalProviderRole->givePermissionTo([
            'view dashboard',
            'use realtime signals',
            'view expert signals',
            'download bots',
            'manage subscription',
            'create expert signals',
            'edit own signals',
        ]);

        $userRole->givePermissionTo([
            'view dashboard',
            'use realtime signals',
            'view expert signals',
            'download bots',
            'manage subscription',
        ]);
    }
}
