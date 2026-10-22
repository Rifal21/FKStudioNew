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
            'waha_url' => 'https://apiwa.fkstudio.id/', 'waha_api_key' => 'rahasia', 'waha_enabled' => '1',
        ])->assertRedirect();

        $this->assertSame('rahasia', $settings->fresh()->waha_api_key);
        $this->assertNotSame('rahasia', DB::table('site_settings')->value('waha_api_key'));
        $this->assertSame('https://apiwa.fkstudio.id', $settings->fresh()->waha_url);
        $this->assertTrue($settings->fresh()->waha_enabled);

        $this->actingAs($this->admin())->post('/dashboard/whatsapp', [
            'waha_url' => 'https://apiwa.fkstudio.id', 'waha_api_key' => '',
        ]);

        $this->assertSame('rahasia', $settings->fresh()->waha_api_key);
        $this->assertFalse($settings->fresh()->waha_enabled);
    }

    public function test_it_lists_every_session_on_the_server_with_the_active_one(): void
    {
        $this->settings();
        Http::fake(['waha.test/api/sessions*' => Http::response([
            ['name' => 'toko', 'status' => 'WORKING', 'me' => ['id' => '6281@c.us', 'pushName' => 'Toko']],
            ['name' => 'fk', 'status' => 'SCAN_QR_CODE'],
        ])]);

        $this->actingAs($this->admin())->getJson('/dashboard/whatsapp/sessions')
            ->assertOk()
            ->assertJsonPath('active', 'fk')
            ->assertJsonPath('sessions.0.name', 'fk')
            ->assertJsonPath('sessions.1.me.pushName', 'Toko');

        Http::assertSent(fn ($r) => str_contains($r->url(), 'all=true'));
    }

    public function test_a_session_can_be_created_from_the_web_and_the_first_one_becomes_the_sender(): void
    {
        $settings = $this->settings(['waha_session' => null]);
        Http::fake(['waha.test/api/sessions' => Http::response(['name' => 'toko', 'status' => 'STARTING'])]);

        $this->actingAs($this->admin())->postJson('/dashboard/whatsapp/sessions', ['name' => 'toko'])
            ->assertOk()->assertJson(['name' => 'toko']);

        Http::assertSent(fn ($r) => $r->method() === 'POST' && $r->url() === 'https://waha.test/api/sessions' && $r['name'] === 'toko' && $r['start'] === true);
        $this->assertSame('toko', $settings->fresh()->waha_session);
    }

    public function test_creating_another_session_keeps_the_current_sender(): void
    {
        $settings = $this->settings(['waha_session' => 'fk']);
        Http::fake(['waha.test/api/sessions' => Http::response(['status' => 'STARTING'])]);

        $this->actingAs($this->admin())->postJson('/dashboard/whatsapp/sessions', ['name' => 'cadangan'])->assertOk();

        $this->assertSame('fk', $settings->fresh()->waha_session);
    }

    public function test_session_names_with_spaces_are_rejected(): void
    {
        $this->settings();

        $this->actingAs($this->admin())->postJson('/dashboard/whatsapp/sessions', ['name' => 'my session'])
            ->assertStatus(422)->assertJsonValidationErrors('name');
    }

    public function test_waha_errors_such_as_the_core_single_session_limit_are_shown_as_is(): void
    {
        $this->settings();
        Http::fake(['waha.test/api/sessions' => Http::response(['message' => "WAHA Core support only 'default' session"], 422)]);

        $this->actingAs($this->admin())->postJson('/dashboard/whatsapp/sessions', ['name' => 'toko'])
            ->assertStatus(422)->assertJsonPath('error', fn ($e) => str_contains($e, 'default'));
    }

    public function test_a_session_can_be_chosen_as_the_sender(): void
    {
        $settings = $this->settings();

        $this->actingAs($this->admin())->postJson('/dashboard/whatsapp/sessions/toko/activate')->assertOk();

        $this->assertSame('toko', $settings->fresh()->waha_session);
    }

    public function test_a_session_can_be_deleted_and_its_qr_read(): void
    {
        $this->settings();
        Http::fake([
            'waha.test/api/sessions/toko' => Http::response(['ok' => true]),
            'waha.test/api/toko/auth/qr' => Http::response(['mimetype' => 'image/png', 'data' => 'AAAA']),
        ]);
        $admin = $this->admin();

        $this->actingAs($admin)->deleteJson('/dashboard/whatsapp/sessions/toko')->assertOk();
        Http::assertSent(fn ($r) => $r->method() === 'DELETE' && $r->url() === 'https://waha.test/api/sessions/toko');

        $this->actingAs($admin)->getJson('/dashboard/whatsapp/sessions/toko/qr')->assertOk()->assertJsonPath('qr.data', 'AAAA');
    }

    public function test_start_upserts_the_chosen_session_in_one_call(): void
    {
        $this->settings();
        Http::fake(['waha.test/api/sessions/start' => Http::response(['name' => 'toko', 'status' => 'STARTING'])]);

        $this->actingAs($this->admin())->postJson('/dashboard/whatsapp/sessions/toko/start')->assertOk();

        Http::assertSentCount(1);
        Http::assertSent(fn ($r) => $r->url() === 'https://waha.test/api/sessions/start' && $r['name'] === 'toko');
    }

    public function test_start_falls_back_to_create_then_start_on_servers_without_the_upsert_endpoint(): void
    {
        $this->settings();
        Http::fake([
            'waha.test/api/sessions/start' => Http::response([], 404),
            'waha.test/api/sessions/toko/start' => Http::response(['status' => 'STARTING']),
            'waha.test/api/sessions/toko' => Http::sequence()->push([], 404)->push(['status' => 'STOPPED']),
            'waha.test/api/sessions' => Http::response(['status' => 'STARTING']),
        ]);
        $admin = $this->admin();

        $this->actingAs($admin)->postJson('/dashboard/whatsapp/sessions/toko/start')->assertOk();
        Http::assertSent(fn ($r) => $r->url() === 'https://waha.test/api/sessions' && $r['name'] === 'toko');

        $this->actingAs($admin)->postJson('/dashboard/whatsapp/sessions/toko/start')->assertOk();
        Http::assertSent(fn ($r) => $r->url() === 'https://waha.test/api/sessions/toko/start');
    }

    public function test_a_number_that_is_not_on_whatsapp_is_refused_before_sending(): void
    {
        $this->settings();
        Http::fake([
            'waha.test/api/checkNumberStatus*' => Http::response(['numberExists' => false, 'chatId' => null]),
            'waha.test/api/sendText' => Http::response(['id' => 'x']),
        ]);

        $this->actingAs($this->admin())->postJson('/dashboard/whatsapp/test', ['phone' => '0812-0000-1111'])
            ->assertStatus(422)->assertJsonPath('error', fn ($e) => str_contains($e, 'tidak terdaftar'));

        Http::assertNotSent(fn ($r) => $r->url() === 'https://waha.test/api/sendText');
    }

    public function test_the_chat_id_confirmed_by_whatsapp_is_the_one_used(): void
    {
        $this->settings();
        Http::fake([
            'waha.test/api/checkNumberStatus*' => Http::response(['numberExists' => true, 'chatId' => '6281200001111@c.us']),
            'waha.test/api/sendText' => Http::response(['id' => 'x']),
        ]);

        $this->actingAs($this->admin())->postJson('/dashboard/whatsapp/test', ['phone' => '0812-0000-1111'])->assertOk();

        Http::assertSent(fn ($r) => str_contains($r->url(), 'checkNumberStatus') && $r['phone'] === '6281200001111' && $r['session'] === 'fk');
    }

    public function test_a_rejected_api_key_comes_back_as_a_readable_error(): void
    {
        $this->settings();
        Http::fake(['*' => Http::response(['message' => 'Unauthorized'], 401)]);

        $this->actingAs($this->admin())->getJson('/dashboard/whatsapp/sessions')
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

    public function test_a_failed_session_is_restarted_in_one_call(): void
    {
        $this->settings();
        Http::fake(['waha.test/api/sessions/toko/restart' => Http::response(['status' => 'STARTING'])]);

        $this->actingAs($this->admin())->postJson('/dashboard/whatsapp/sessions/toko/restart')->assertOk();
        Http::assertSent(fn ($r) => $r->url() === 'https://waha.test/api/sessions/toko/restart');
    }

    public function test_restart_falls_back_to_stop_then_start_even_if_the_crashed_session_refuses_to_stop(): void
    {
        $this->settings();
        Http::fake([
            'waha.test/api/sessions/toko/restart' => Http::response([], 404),
            'waha.test/api/sessions/toko/stop' => Http::response(['message' => 'closed'], 500),
            'waha.test/api/sessions/toko/start' => Http::response(['status' => 'STARTING']),
        ]);

        $this->actingAs($this->admin())->postJson('/dashboard/whatsapp/sessions/toko/restart')->assertOk();
        Http::assertSent(fn ($r) => $r->url() === 'https://waha.test/api/sessions/toko/start');
    }

    public function test_sending_through_a_session_that_is_not_working_explains_what_to_do(): void
    {
        $this->settings(['waha_enabled' => true, 'waha_session' => 'fkstudio']);
        Http::fake(['waha.test/api/sessions/fkstudio' => Http::response(['name' => 'fkstudio', 'status' => 'FAILED'])]);
        $invoice = \App\Models\Invoice::create([
            'invoice_number' => 'INV-9', 'client_name' => 'Budi', 'client_phone' => '0811111111',
            'issue_date' => '2026-10-22', 'total_amount' => 1000, 'status' => 'Draft',
            'discount' => 0, 'discount_type' => 'percent', 'tax' => 0, 'tax_type' => 'percent',
        ]);

        $this->assertFalse(app(\App\Services\InvoiceWhatsApp::class)->send($invoice));

        $error = $invoice->fresh()->wa_error;
        $this->assertStringContainsString('FAILED', $error);
        $this->assertStringContainsString('Dashboard > WhatsApp', $error);
        Http::assertNotSent(fn ($r) => str_contains($r->url(), 'sendText'));
    }
}
