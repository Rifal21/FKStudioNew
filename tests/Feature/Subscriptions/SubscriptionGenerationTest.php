<?php

namespace Tests\Feature\Subscriptions;

use App\Models\Invoice;
use App\Models\SiteSetting;
use App\Models\Subscription;
use App\Services\InvoiceWhatsApp;
use App\Services\SubscriptionInvoiceGenerator;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SubscriptionGenerationTest extends TestCase
{
    use RefreshDatabase;

    private function subscription(array $overrides = [], ?array $items = null): Subscription
    {
        $subscription = Subscription::create($overrides + [
            'title' => 'Hosting & maintenance',
            'client_name' => 'Toko Maju',
            'client_phone' => '0812-3456-7890',
            'billing_day' => 5,
            'due_days' => 7,
            'start_date' => '2026-10-01',
            'next_run_date' => '2026-11-05',
            'status' => Subscription::ACTIVE,
            'notes' => 'Langganan {bulan} {tahun}',
        ]);

        foreach ($items ?? [
            ['description' => 'Hosting {bulan} {tahun}', 'quantity' => 1, 'unit_price' => 150000],
            ['description' => 'Maintenance', 'quantity' => 2, 'unit_price' => 100000],
            ['description' => 'Domain', 'quantity' => 1, 'unit_price' => 50000],
        ] as $position => $item) {
            $subscription->items()->create($item + ['position' => $position]);
        }

        return $subscription->fresh('items');
    }

    public function test_it_builds_an_invoice_with_items_in_order_and_fills_placeholders(): void
    {
        $subscription = $this->subscription(['discount' => 10, 'tax' => 11]);

        $invoice = app(SubscriptionInvoiceGenerator::class)->generate($subscription, Carbon::parse('2026-11-05'));

        $this->assertSame('Draft', $invoice->status);
        $this->assertSame('2026-11-12', $invoice->due_date->toDateString());
        $this->assertSame('Langganan November 2026', $invoice->notes);
        $this->assertSame(
            ['Hosting November 2026', 'Maintenance', 'Domain'],
            $invoice->items->pluck('description')->all(),
        );
        // 400.000 - 10% = 360.000, + 11% = 399.600
        $this->assertEquals(399600, $invoice->total_amount);
        $this->assertSame('2026-12-05', $subscription->fresh()->next_run_date->toDateString());
    }

    public function test_the_same_month_is_never_billed_twice(): void
    {
        $subscription = $this->subscription();
        $generator = app(SubscriptionInvoiceGenerator::class);

        $generator->generate($subscription, Carbon::parse('2026-11-05'));
        // Simulate a rerun that still sees the old schedule.
        $subscription->update(['next_run_date' => '2026-11-05']);
        $second = $generator->generate($subscription->fresh(), Carbon::parse('2026-11-05'));

        $this->assertNull($second);
        $this->assertSame(1, Invoice::count());
        $this->assertSame('2026-12-05', $subscription->fresh()->next_run_date->toDateString());
    }

    public function test_billing_day_31_is_clamped_to_the_last_day_of_short_months(): void
    {
        $subscription = $this->subscription(['billing_day' => 31, 'next_run_date' => '2027-01-31']);

        app(SubscriptionInvoiceGenerator::class)->generate($subscription, Carbon::parse('2027-01-31'));
        $this->assertSame('2027-02-28', $subscription->fresh()->next_run_date->toDateString());

        app(SubscriptionInvoiceGenerator::class)->generate($subscription->fresh(), Carbon::parse('2027-02-28'));
        $this->assertSame('2027-03-31', $subscription->fresh()->next_run_date->toDateString());
    }

    public function test_the_subscription_ends_after_the_last_billing_date(): void
    {
        $subscription = $this->subscription(['end_date' => '2026-12-31']);
        $generator = app(SubscriptionInvoiceGenerator::class);

        $generator->generate($subscription, Carbon::parse('2026-11-05'));
        $generator->generate($subscription->fresh(), Carbon::parse('2026-12-05'));

        $subscription = $subscription->fresh();
        $this->assertSame(Subscription::ENDED, $subscription->status);
        $this->assertNull($subscription->next_run_date);
    }

    public function test_command_bills_due_subscriptions_and_skips_paused_and_future_ones(): void
    {
        $this->travelTo(Carbon::parse('2026-11-05 06:00', 'Asia/Jakarta'));

        $due = $this->subscription();
        $this->subscription(['client_name' => 'Dijeda', 'status' => Subscription::PAUSED]);
        $this->subscription(['client_name' => 'Belum waktunya', 'next_run_date' => '2026-11-20']);

        $this->artisan('subscriptions:generate')->assertSuccessful();

        $this->assertSame(1, Invoice::count());
        $this->assertSame($due->id, Invoice::first()->subscription_id);
    }

    public function test_command_catches_up_a_missed_day(): void
    {
        $this->travelTo(Carbon::parse('2026-11-07 06:00', 'Asia/Jakarta'));
        $this->subscription();

        $this->artisan('subscriptions:generate')->assertSuccessful();

        $this->assertSame(1, Invoice::count());
        $this->assertSame('2026-11-07', Invoice::first()->issue_date->toDateString());
    }

    public function test_auto_send_delivers_text_and_pdf_over_waha_and_marks_the_invoice_sent(): void
    {
        $this->travelTo(Carbon::parse('2026-11-05 06:00', 'Asia/Jakarta'));
        SiteSetting::create([
            'site_name' => 'FKStudio',
            'waha_enabled' => true,
            'waha_url' => 'https://waha.test',
            'waha_api_key' => 'secret',
            'waha_session' => 'fk',
        ]);
        Http::fake(['waha.test/*' => Http::response(['id' => 'x'])]);

        $this->subscription(['auto_send' => true]);
        $this->artisan('subscriptions:generate')->assertSuccessful();

        $invoice = Invoice::first();
        $this->assertSame('Sent', $invoice->status);
        $this->assertNotNull($invoice->wa_sent_at);

        Http::assertSent(fn ($r) => $r->url() === 'https://waha.test/api/sendText'
            && $r['chatId'] === '6281234567890@c.us'
            && $r['session'] === 'fk'
            && $r->hasHeader('X-Api-Key', 'secret')
            && str_contains($r['text'], $invoice->invoice_number));
        Http::assertSent(fn ($r) => $r->url() === 'https://waha.test/api/sendFile'
            && $r['file']['mimetype'] === 'application/pdf');
    }

    public function test_a_failed_send_keeps_the_invoice_as_draft_with_the_reason(): void
    {
        SiteSetting::create(['site_name' => 'FKStudio', 'waha_enabled' => true, 'waha_url' => 'https://waha.test', 'waha_api_key' => 'secret']);
        Http::fake(['waha.test/*' => Http::response(['message' => 'Session is not ready'], 422)]);

        $subscription = $this->subscription();
        $invoice = app(SubscriptionInvoiceGenerator::class)->generate($subscription, Carbon::parse('2026-11-05'));

        $this->assertFalse(app(InvoiceWhatsApp::class)->send($invoice));
        $invoice = $invoice->fresh();
        $this->assertSame('Draft', $invoice->status);
        $this->assertNull($invoice->wa_sent_at);
        $this->assertStringContainsString('Session is not ready', $invoice->wa_error);
    }

    public function test_a_missing_phone_number_is_reported_instead_of_sent(): void
    {
        SiteSetting::create(['site_name' => 'FKStudio', 'waha_enabled' => true, 'waha_api_key' => 'secret']);
        Http::fake();

        $subscription = $this->subscription(['client_phone' => null]);
        $invoice = app(SubscriptionInvoiceGenerator::class)->generate($subscription, Carbon::parse('2026-11-05'));

        $this->assertFalse(app(InvoiceWhatsApp::class)->send($invoice));
        $this->assertStringContainsString('Nomor WhatsApp', $invoice->fresh()->wa_error);
        Http::assertNothingSent();
    }

    public function test_numbers_that_arrive_as_strings_do_not_break_the_schedule(): void
    {
        // MySQL returns integer columns as strings; Carbon's day()/addDays() reject those.
        $subscription = $this->subscription(['billing_day' => '31', 'due_days' => '14', 'next_run_date' => '2027-01-31']);
        $subscription->setRawAttributes(array_merge($subscription->getAttributes(), ['billing_day' => '31', 'due_days' => '14']), true);

        $invoice = app(SubscriptionInvoiceGenerator::class)->generate($subscription, Carbon::parse('2027-01-31'));

        $this->assertSame('2027-02-14', $invoice->due_date->toDateString());
        $this->assertSame('2027-02-28', $subscription->fresh()->next_run_date->toDateString());
    }

    public function test_a_subscription_goes_out_at_its_own_time_not_before(): void
    {
        $this->subscription(['billing_time' => '09:30']);

        $this->travelTo(Carbon::parse('2026-11-05 09:29', 'Asia/Jakarta'));
        $this->artisan('subscriptions:generate')->assertSuccessful();
        $this->assertSame(0, Invoice::count());

        $this->travelTo(Carbon::parse('2026-11-05 09:30', 'Asia/Jakarta'));
        $this->artisan('subscriptions:generate')->assertSuccessful();
        $this->assertSame(1, Invoice::count());

        // The minute-by-minute runs that follow must not bill the same month again.
        $this->travelTo(Carbon::parse('2026-11-05 09:31', 'Asia/Jakarta'));
        $this->artisan('subscriptions:generate')->assertSuccessful();
        $this->assertSame(1, Invoice::count());
    }

    public function test_two_subscriptions_with_different_times_are_sent_separately(): void
    {
        $this->subscription(['client_name' => 'Pagi', 'billing_time' => '08:00']);
        $this->subscription(['client_name' => 'Sore', 'billing_time' => '16:00']);

        $this->travelTo(Carbon::parse('2026-11-05 10:00', 'Asia/Jakarta'));
        $this->artisan('subscriptions:generate')->assertSuccessful();

        $this->assertSame(['Pagi'], Invoice::pluck('client_name')->all());
    }

    public function test_failed_sends_are_retried_hourly_and_stop_after_five_attempts(): void
    {
        SiteSetting::create(['site_name' => 'FKStudio', 'waha_enabled' => true, 'waha_url' => 'https://waha.test', 'waha_api_key' => 'k']);
        Http::fake(['waha.test/*' => Http::response(['message' => 'down'], 500)]);

        $this->travelTo(Carbon::parse('2026-11-05 09:00', 'Asia/Jakarta'));
        $this->subscription(['auto_send' => true, 'billing_time' => '09:00']);
        $this->artisan('subscriptions:generate')->assertSuccessful();
        $invoice = Invoice::first();
        $this->assertSame(1, $invoice->wa_attempts);

        // Not on the hour: no retry.
        $this->travelTo(Carbon::parse('2026-11-05 09:30', 'Asia/Jakarta'));
        $this->artisan('subscriptions:generate')->assertSuccessful();
        $this->assertSame(1, $invoice->fresh()->wa_attempts);

        foreach (['10:00', '11:00', '12:00', '13:00', '14:00', '15:00'] as $time) {
            $this->travelTo(Carbon::parse("2026-11-05 $time", 'Asia/Jakarta'));
            $this->artisan('subscriptions:generate')->assertSuccessful();
        }

        $this->assertSame(5, $invoice->fresh()->wa_attempts);
    }
}
