<?php

namespace Tests\Feature\Subscriptions;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'super_admin']);
    }

    private function settings(array $attrs = []): SiteSetting
    {
        return SiteSetting::create($attrs + ['site_name' => 'FKStudio', 'waha_url' => 'https://waha.test', 'waha_api_key' => 'k', 'waha_session' => 'fk']);
    }

    public function test_the_api_key_is_stored_encrypted_and_a_blank_field_keeps_it(): void
    {
        $settings = $this->settings(['waha_api_key' => null]);

        $this->actingAs($this->admin())->post('/dashboard/whatsapp', [
            'waha_url' => 'https://apiwa.fkstudio.id/', 'waha_session' => 'fk', 'waha_api_key' => 'rahasia', 'waha_enabled' => '1',
        ])->assertRedirect();

        $this->assertSame('rahasia', $settings->fresh()->waha_api_key);
        $this->assertNotSame('rahasia', DB::table('site_settings')->value('waha_api_key'));
        $this->assertSame('https://apiwa.fkstudio.id', $settings->fresh()->waha_url);
        $this->assertTrue($settings->fresh()->waha_enabled);

        $this->actingAs($this->admin())->post('/dashboard/whatsapp', [
            'waha_url' => 'https://apiwa.fkstudio.id', 'waha_session' => 'fk', 'waha_api_key' => '',
        ]);

        $this->assertSame('rahasia', $settings->fresh()->waha_api_key);
        $this->assertFalse($settings->fresh()->waha_enabled);
    }

    public function test_session_names_with_spaces_are_rejected(): void
    {
        $this->settings();

        $this->actingAs($this->admin())->post('/dashboard/whatsapp', [
            'waha_url' => 'https://waha.test', 'waha_session' => 'my session',
        ])->assertSessionHasErrors('waha_session');
    }

    public function test_status_reports_a_session_that_does_not_exist_yet(): void
    {
        $this->settings();
        Http::fake(['waha.test/api/sessions/fk' => Http::response(['message' => 'nf'], 404)]);

        $this->actingAs($this->admin())->getJson('/dashboard/whatsapp/status')
            ->assertOk()->assertJson(['exists' => false, 'status' => 'NOT_CREATED']);
    }

    public function test_start_creates_the_session_when_missing_and_restarts_it_otherwise(): void
    {
        $this->settings();
        Http::fake([
            'waha.test/api/sessions/fk/start' => Http::response(['status' => 'STARTING']),
            'waha.test/api/sessions/fk' => Http::sequence()->push([], 404)->push(['status' => 'STOPPED']),
            'waha.test/api/sessions' => Http::response(['status' => 'STARTING']),
        ]);
        $admin = $this->admin();

        $this->actingAs($admin)->postJson('/dashboard/whatsapp/start')->assertOk();
        Http::assertSent(fn ($r) => $r->url() === 'https://waha.test/api/sessions' && $r['name'] === 'fk');

        $this->actingAs($admin)->postJson('/dashboard/whatsapp/start')->assertOk();
        Http::assertSent(fn ($r) => $r->url() === 'https://waha.test/api/sessions/fk/start');
    }

    public function test_a_rejected_api_key_comes_back_as_a_readable_error(): void
    {
        $this->settings();
        Http::fake(['*' => Http::response(['message' => 'Unauthorized'], 401)]);

        $this->actingAs($this->admin())->getJson('/dashboard/whatsapp/status')
            ->assertStatus(422)->assertJsonPath('error', fn ($e) => str_contains($e, 'API key'));
    }

    public function test_the_test_message_goes_to_the_normalized_number(): void
    {
        $this->settings();
        Http::fake(['waha.test/*' => Http::response(['id' => 'x'])]);

        $this->actingAs($this->admin())->postJson('/dashboard/whatsapp/test', ['phone' => '0812-0000-1111'])->assertOk();

        Http::assertSent(fn ($r) => $r->url() === 'https://waha.test/api/sendText' && $r['chatId'] === '6281200001111@c.us');
    }

    public function test_the_page_never_prints_the_stored_key(): void
    {
        $this->settings(['waha_api_key' => 'super-secret-key']);

        $this->actingAs($this->admin())->get('/dashboard/whatsapp')->assertOk()->assertDontSee('super-secret-key');
    }
}
