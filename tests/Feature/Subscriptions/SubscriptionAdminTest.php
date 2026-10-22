<?php

namespace Tests\Feature\Subscriptions;

use App\Models\Invoice;
use App\Models\SiteSetting;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SubscriptionAdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'super_admin']);
    }

    private function payload(array $overrides = []): array
    {
        return $overrides + [
            'title' => 'Hosting',
            'client_name' => 'Toko Maju',
            'client_phone' => '081234567890',
            'billing_day' => 5,
            'billing_time' => '09:30',
            'due_days' => 7,
            'start_date' => '2026-10-01',
            'discount' => 0,
            'tax' => 0,
            'items' => [
                ['description' => 'Hosting {bulan}', 'quantity' => 1, 'unit_price' => 150000],
                ['description' => 'Domain', 'quantity' => 1, 'unit_price' => 50000],
            ],
        ];
    }

    public function test_only_super_admins_can_open_subscriptions(): void
    {
        $this->get('/dashboard/subscriptions')->assertRedirect();
        $this->actingAs(User::factory()->create(['role' => 'user']))->get('/dashboard/subscriptions')->assertForbidden();
        $this->actingAs($this->admin())->get('/dashboard/subscriptions')->assertOk();
    }

    public function test_it_creates_a_subscription_with_ordered_items_and_a_schedule(): void
    {
        $this->travelTo(Carbon::parse('2026-10-22 09:00', 'Asia/Jakarta'));

        $this->actingAs($this->admin())->post('/dashboard/subscriptions', $this->payload())->assertRedirect();

        $subscription = Subscription::firstOrFail();
        $this->assertSame(['Hosting {bulan}', 'Domain'], $subscription->items->pluck('description')->all());
        $this->assertSame('2026-11-05', $subscription->next_run_date->toDateString());
        $this->assertSame('active', $subscription->status);
    }

    public function test_the_send_time_is_saved_and_must_be_a_valid_time(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/dashboard/subscriptions', $this->payload(['billing_time' => '25:99']))
            ->assertSessionHasErrors('billing_time');

        $this->actingAs($admin)->post('/dashboard/subscriptions', $this->payload(['billing_time' => '14:15']))->assertRedirect();
        $this->assertSame('14:15', Subscription::first()->billing_time);
    }

    public function test_it_rejects_a_billing_day_outside_1_to_31(): void
    {
        $this->actingAs($this->admin())
            ->post('/dashboard/subscriptions', $this->payload(['billing_day' => 32]))
            ->assertSessionHasErrors('billing_day');

        $this->assertSame(0, Subscription::count());
    }

    public function test_editing_replaces_items_in_the_new_order(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post('/dashboard/subscriptions', $this->payload());
        $subscription = Subscription::first();

        $this->actingAs($admin)->patch("/dashboard/subscriptions/{$subscription->id}", $this->payload([
            'items' => [
                ['description' => 'Domain', 'quantity' => 1, 'unit_price' => 50000],
                ['description' => 'Hosting {bulan}', 'quantity' => 1, 'unit_price' => 150000],
            ],
        ]))->assertRedirect();

        $this->assertSame(['Domain', 'Hosting {bulan}'], $subscription->fresh()->items->pluck('description')->all());
    }

    public function test_resuming_does_not_bill_months_skipped_while_paused(): void
    {
        $admin = $this->admin();
        $this->travelTo(Carbon::parse('2026-10-22 09:00', 'Asia/Jakarta'));
        $this->actingAs($admin)->post('/dashboard/subscriptions', $this->payload());
        $subscription = Subscription::first();

        $this->actingAs($admin)->post("/dashboard/subscriptions/{$subscription->id}/pause");
        $this->assertSame('paused', $subscription->fresh()->status);

        $this->travelTo(Carbon::parse('2027-02-10 09:00', 'Asia/Jakarta'));
        $this->actingAs($admin)->post("/dashboard/subscriptions/{$subscription->id}/resume");

        $subscription = $subscription->fresh();
        $this->assertSame('active', $subscription->status);
        $this->assertSame('2027-03-05', $subscription->next_run_date->toDateString());
        $this->assertSame(0, Invoice::count());
    }

    public function test_generate_now_creates_the_invoice_and_moves_the_schedule(): void
    {
        $admin = $this->admin();
        $this->travelTo(Carbon::parse('2026-10-22 09:00', 'Asia/Jakarta'));
        $this->actingAs($admin)->post('/dashboard/subscriptions', $this->payload());
        $subscription = Subscription::first();

        $this->actingAs($admin)->post("/dashboard/subscriptions/{$subscription->id}/generate")->assertRedirect();

        $this->assertSame(1, Invoice::count());
        $this->assertSame('2026-12-05', $subscription->fresh()->next_run_date->toDateString());
    }

    public function test_deleting_a_subscription_keeps_its_invoices(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post('/dashboard/subscriptions', $this->payload());
        $subscription = Subscription::first();
        $this->actingAs($admin)->post("/dashboard/subscriptions/{$subscription->id}/generate");

        $this->actingAs($admin)->delete("/dashboard/subscriptions/{$subscription->id}")->assertRedirect();

        $this->assertSame(0, Subscription::count());
        $this->assertSame(1, Invoice::count());
        $this->assertNull(Invoice::first()->subscription_id);
    }

    public function test_an_invoice_can_be_sent_by_whatsapp_from_the_admin(): void
    {
        SiteSetting::create(['site_name' => 'FKStudio', 'waha_enabled' => true, 'waha_url' => 'https://waha.test', 'waha_api_key' => 'k']);
        Http::fake(['waha.test/*' => Http::response(['id' => 'x'])]);
        $invoice = Invoice::create([
            'invoice_number' => 'INV-1', 'client_name' => 'Budi', 'client_phone' => '0811111111',
            'issue_date' => '2026-10-22', 'total_amount' => 1000, 'status' => 'Draft',
            'discount' => 0, 'discount_type' => 'percent', 'tax' => 0, 'tax_type' => 'percent',
        ]);

        $this->actingAs($this->admin())->post("/dashboard/invoices/{$invoice->id}/whatsapp")->assertRedirect();

        $this->assertNotNull($invoice->fresh()->wa_sent_at);
        $this->assertSame('Sent', $invoice->fresh()->status);
    }
}
