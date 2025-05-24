<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\AuditLog;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AuditLog>
 */
class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $actions = ['create', 'update', 'delete', 'login', 'logout', 'view', 'export'];
        $types = ['user', 'admin', 'system', 'security'];
        $levels = ['info', 'warning', 'error', 'critical'];

        return [
            'user_id' => User::factory(),
            'action' => $this->faker->randomElement($actions),
            'type' => $this->faker->randomElement($types),
            'description' => $this->faker->sentence(),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'level' => $this->faker->randomElement($levels),
        ];
    }

    /**
     * Indicate that the audit log is for admin actions.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'admin',
            'action' => $this->faker->randomElement(['create_user', 'update_settings', 'view_reports', 'manage_subscriptions']),
        ]);
    }

    /**
     * Indicate that the audit log is for security events.
     */
    public function security(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'security',
            'level' => $this->faker->randomElement(['warning', 'error', 'critical']),
            'action' => $this->faker->randomElement(['failed_login', 'suspicious_activity', 'password_change', 'api_key_generated']),
        ]);
    }

    /**
     * Indicate that the audit log is for system events.
     */
    public function system(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'system',
            'user_id' => null, // System events don't have a user
            'action' => $this->faker->randomElement(['backup_created', 'maintenance_mode', 'cache_cleared', 'system_update']),
        ]);
    }
}
