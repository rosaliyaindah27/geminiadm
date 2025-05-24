<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\SystemSetting;
use App\Models\AuditLog;
use App\Services\EmailNotificationService;
use App\Helpers\SettingsHelper;

class AdminSystemTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->adminUser = User::factory()->create([
            'email' => 'admin@geminiprotrader.com',
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);
    }

    /** @test */
    public function admin_can_access_dashboard()
    {
        $response = $this->actingAs($this->adminUser)
            ->get('/admin');

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
    }

    /** @test */
    public function admin_can_view_notifications_management()
    {
        $response = $this->actingAs($this->adminUser)
            ->get('/admin/notifications');

        $response->assertStatus(200);
        $response->assertViewIs('admin.notifications.index');
    }

    /** @test */
    public function admin_can_view_audit_logs()
    {
        // Create some audit logs
        AuditLog::factory()->count(5)->create();

        $response = $this->actingAs($this->adminUser)
            ->get('/admin/audit');

        $response->assertStatus(200);
        $response->assertViewIs('admin.audit.index');
    }

    /** @test */
    public function admin_can_view_content_management()
    {
        $response = $this->actingAs($this->adminUser)
            ->get('/admin/content');

        $response->assertStatus(200);
        $response->assertViewIs('admin.content.index');
    }

    /** @test */
    public function system_settings_can_be_created_and_retrieved()
    {
        $setting = SystemSetting::create([
            'key' => 'test_setting',
            'value' => 'test_value',
            'type' => 'string',
            'description' => 'Test setting',
            'group' => 'test',
        ]);

        $this->assertDatabaseHas('system_settings', [
            'key' => 'test_setting',
            'value' => 'test_value',
        ]);

        $value = SystemSetting::get('test_setting');
        $this->assertEquals('test_value', $value);
    }

    /** @test */
    public function settings_helper_works_correctly()
    {
        SystemSetting::create([
            'key' => 'helper_test',
            'value' => 'helper_value',
            'type' => 'string',
            'group' => 'test',
        ]);

        $value = SettingsHelper::get('helper_test');
        $this->assertEquals('helper_value', $value);

        $defaultValue = SettingsHelper::get('non_existent_key', 'default');
        $this->assertEquals('default', $defaultValue);
    }

    /** @test */
    public function audit_log_can_be_created()
    {
        AuditLog::log('test_action', 'Test description', null, null, null, 'info', 'admin');

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'test_action',
            'description' => 'Test description',
            'level' => 'info',
            'type' => 'admin',
        ]);
    }

    /** @test */
    public function email_notification_service_can_send_welcome_email()
    {
        $user = User::factory()->create();
        $emailService = new EmailNotificationService();

        // Mock the mail facade to prevent actual email sending
        \Mail::fake();

        $result = $emailService->sendWelcomeEmail($user);

        $this->assertTrue($result);
        \Mail::assertSent(\App\Mail\WelcomeEmail::class);
    }

    /** @test */
    public function api_endpoints_require_authentication()
    {
        $response = $this->getJson('/api/v1/signals');
        $response->assertStatus(401);

        $response = $this->getJson('/api/v1/user/profile');
        $response->assertStatus(401);
    }

    /** @test */
    public function api_endpoints_work_with_valid_api_key()
    {
        $user = User::factory()->create([
            'api_key' => 'test-api-key-123',
            'api_enabled' => true,
        ]);

        $response = $this->getJson('/api/v1/user/profile', [
            'X-API-Key' => 'test-api-key-123'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }

    /** @test */
    public function webhook_endpoint_validates_signature()
    {
        $payload = json_encode(['test' => 'data']);
        $signature = hash_hmac('sha512', $payload, config('services.nowpayments.ipn_secret'));

        $response = $this->postJson('/api/webhooks/nowpayments', json_decode($payload, true), [
            'X-Nowpayments-Sig' => $signature
        ]);

        // Should not return 401 (invalid signature)
        $this->assertNotEquals(401, $response->status());
    }

    /** @test */
    public function admin_routes_are_protected()
    {
        $regularUser = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($regularUser)
            ->get('/admin');

        $response->assertStatus(403);
    }

    /** @test */
    public function system_settings_seeder_creates_default_settings()
    {
        $this->artisan('db:seed', ['--class' => 'SystemSettingsSeeder']);

        $this->assertDatabaseHas('system_settings', [
            'key' => 'site_name',
            'value' => 'Gemini Pro Trader',
        ]);

        $this->assertDatabaseHas('system_settings', [
            'key' => 'nowpayments_api_key',
            'value' => 'A2BYB64-8DFMH2B-PESRT93-TYZK4GK',
        ]);
    }

    /** @test */
    public function email_templates_render_correctly()
    {
        $user = User::factory()->create();

        $welcomeView = view('emails.welcome', compact('user'));
        $this->assertStringContainsString($user->name, $welcomeView->render());

        $subscriptionView = view('emails.subscription-activated', compact('user'));
        $this->assertStringContainsString('subscription has been successfully activated', $subscriptionView->render());
    }

    /** @test */
    public function rate_limiting_works_for_api()
    {
        $user = User::factory()->create([
            'api_key' => 'rate-limit-test-key',
            'api_enabled' => true,
        ]);

        // Make multiple requests to test rate limiting
        for ($i = 0; $i < 5; $i++) {
            $response = $this->getJson('/api/v1/user/profile', [
                'X-API-Key' => 'rate-limit-test-key'
            ]);
            
            if ($i < 4) {
                $response->assertStatus(200);
            }
        }

        // The response should include rate limit headers
        $response->assertHeader('X-RateLimit-Limit');
        $response->assertHeader('X-RateLimit-Remaining');
    }
}