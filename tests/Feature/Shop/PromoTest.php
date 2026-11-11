<?php

namespace Tests\Feature\Shop;

use App\Models\PromoContent;
use App\Models\Voucher;
use App\Services\Promo\PromoException;
use App\Services\Promo\PromoFormats;
use App\Services\Promo\PromoWriter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Replaces the one method that calls Claude, so no test spends tokens or needs a key. */
class StubbedPromoWriter extends PromoWriter
{
    public array $seen = [];

    public function __construct(private array|PromoException $reply = [])
    {
    }

    protected function complete(string $system, string $user, array $schema): array
    {
        $this->seen = compact('system', 'user', 'schema');

        if ($this->reply instanceof PromoException) {
            throw $this->reply;
        }

        return ['text' => json_encode($this->reply ?: $this->defaults($schema)), 'model' => 'claude-sonnet-5-5', 'input_tokens' => 1200, 'output_tokens' => 800];
    }

    private function defaults(array $schema): array
    {
        $all = [
            'instagram' => "Halo! {nama} sekarang hanya {harga}.\n\nCek di {link}",
            'facebook' => "Pernah bingung soal ini?\n\n{nama} membantu Anda, cuma {harga}.\n\n{link}",
            'whatsapp' => '*{nama}* cuma {harga}. Beli: {link}',
            'banner' => ['headline' => 'Belajar lebih mudah', 'subheadline' => 'Mulai dari {harga}'],
            'email' => ['subject' => 'Baru: {nama}', 'body' => 'Selengkapnya di {link}'],
            'ads' => ['Satu', 'Dua', 'Tiga'],
            'tagline' => 'Praktis tiap hari',
            'hashtags' => ['ebook', '#parenting', 'belajar anak'],
        ];

        return array_intersect_key($all, $schema['properties']);
    }
}

class PromoTest extends TestCase
{
    use BuildsShop, RefreshDatabase;

    private StubbedPromoWriter $writer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->shopSettings();
        $this->writer = new StubbedPromoWriter();
        $this->app->instance(PromoWriter::class, $this->writer);
        config(['services.anthropic.key' => 'test-key', 'services.anthropic.promo_daily_limit' => 30]);
    }

    private function payload(array $overrides = []): array
    {
        return $overrides + ['locale' => 'id', 'goal' => 'launch', 'tone' => 'casual', 'formats' => ['instagram', 'whatsapp', 'banner', 'hashtags']];
    }

    private function product()
    {
        return $this->digitalProduct(['name_id' => 'Parenting Islami Praktis', 'short_description_id' => 'E-book 80 halaman untuk orang tua.'], 45000);
    }

    public function test_only_admins_can_use_the_promo_pages(): void
    {
        $this->get('/dashboard/shop/promos')->assertRedirect('/login');
        $this->actingAs($this->customer())->get('/dashboard/shop/promos')->assertForbidden();
        $this->actingAs($this->customer())->post('/dashboard/shop/promos', $this->payload())->assertForbidden();
    }

    public function test_admin_generates_content_with_real_facts_swapped_in_for_placeholders(): void
    {
        $product = $this->product();

        $response = $this->actingAs($this->admin())->post('/dashboard/shop/promos', $this->payload(['product_id' => $product->id]));

        $promo = PromoContent::firstOrFail();
        $response->assertRedirect(route('dashboard.shop.promos.show', $promo));

        $this->assertSame('Halo! Parenting Islami Praktis sekarang hanya Rp 45.000.'."\n\n".'Cek di '.route('shop.show', $product->slug), $promo->content['instagram']);
        $this->assertSame(['headline' => 'Belajar lebih mudah', 'subheadline' => 'Mulai dari Rp 45.000'], $promo->content['banner']);
        $this->assertSame(['#ebook', '#parenting', '#belajaranak'], $promo->content['hashtags']);
        $this->assertSame([], $promo->warnings);
        $this->assertSame('draft', $promo->status);
        $this->assertSame(2000, $promo->input_tokens + $promo->output_tokens);
        $this->assertSame('claude-sonnet-5-5', $promo->model);

        $this->actingAs($this->admin())->get(route('dashboard.shop.promos.show', $promo))->assertOk()->assertSee('Parenting Islami Praktis')->assertSee('Caption Instagram');
    }

    public function test_facebook_post_is_its_own_format_with_the_real_link(): void
    {
        $product = $this->product();

        $this->actingAs($this->admin())->post('/dashboard/shop/promos', $this->payload(['product_id' => $product->id, 'formats' => ['facebook']]));

        $promo = PromoContent::firstOrFail();
        $this->assertSame(['facebook'], $promo->formats);
        $this->assertSame("Pernah bingung soal ini?\n\nParenting Islami Praktis membantu Anda, cuma Rp 45.000.\n\n".route('shop.show', $product->slug), $promo->content['facebook']);
        $this->assertSame([], $promo->warnings);
        $this->assertStringContainsString('facebook:', $this->writer->seen['user']);

        $this->actingAs($this->admin())->get(route('dashboard.shop.promos.show', $promo))->assertOk()->assertSee('Postingan Facebook');
        $this->actingAs($this->admin())->get('/dashboard/shop/promos/create')->assertOk()->assertSee('Postingan Facebook');
    }

    public function test_only_the_chosen_formats_are_requested_from_claude(): void
    {
        $product = $this->product();

        $this->actingAs($this->admin())->post('/dashboard/shop/promos', $this->payload(['product_id' => $product->id, 'formats' => ['tagline']]));

        $this->assertSame(['tagline'], array_keys($this->writer->seen['schema']['properties']));
        $this->assertSame(['tagline'], $this->writer->seen['schema']['required']);
        $this->assertFalse($this->writer->seen['schema']['additionalProperties']);
        $this->assertStringContainsString('Never write a price', $this->writer->seen['system']);
        $this->assertStringContainsString('name: Parenting Islami Praktis', $this->writer->seen['user']);
        $this->assertStringContainsString('{harga}', $this->writer->seen['user']);
        $this->assertSame(['tagline'], PromoContent::first()->formats);
    }

    public function test_numbers_links_and_leftover_placeholders_that_the_product_does_not_back_are_flagged(): void
    {
        $this->app->instance(PromoWriter::class, new StubbedPromoWriter([
            'instagram' => 'Diskon 70% hanya hari ini! Sudah dibeli 1.200 orang. Beli di https://toko-lain.test/x atau {link}. Kode {kode}.',
        ]));
        $product = $this->product();

        $this->actingAs($this->admin())->post('/dashboard/shop/promos', $this->payload(['product_id' => $product->id, 'formats' => ['instagram']]));

        $warnings = implode(' ', PromoContent::firstOrFail()->warnings);
        $this->assertStringContainsString('70', $warnings);
        $this->assertStringContainsString('1200', $warnings);
        $this->assertStringContainsString('https://toko-lain.test/x', $warnings);
        $this->assertStringContainsString('{kode}', $warnings);
        $this->assertStringNotContainsString(route('shop.show', $product->slug), $warnings, 'the real product link is fine');
    }

    public function test_a_number_taken_from_the_product_description_is_not_flagged(): void
    {
        $this->app->instance(PromoWriter::class, new StubbedPromoWriter(['tagline' => 'Buku kerja 80 halaman, 10 bab']));
        $product = $this->digitalProduct(['name_id' => 'Buku', 'short_description_id' => 'Buku kerja 80 halaman dengan 10 bab.'], 45000);

        $this->actingAs($this->admin())->post('/dashboard/shop/promos', $this->payload(['product_id' => $product->id, 'formats' => ['tagline']]));

        $this->assertSame([], PromoContent::firstOrFail()->warnings);
    }

    public function test_crossed_out_price_and_voucher_become_placeholders_with_real_values(): void
    {
        $product = $this->product();
        $product->variants()->update(['compare_at_price' => 60000]);
        Voucher::create(['code' => 'HEMAT10', 'type' => 'percent', 'value' => 10, 'is_active' => true, 'scope' => 'shop']);
        $this->app->instance(PromoWriter::class, $writer = new StubbedPromoWriter(['tagline' => 'x', 'whatsapp' => 'Dari {harga_coret} jadi {harga} (-{diskon}). Pakai {kode} untuk {potongan} lagi.']));

        $this->actingAs($this->admin())->post('/dashboard/shop/promos', $this->payload(['product_id' => $product->id, 'formats' => ['whatsapp'], 'voucher_code' => 'HEMAT10']));

        $promo = PromoContent::firstOrFail();
        $this->assertSame('Dari Rp 60.000 jadi Rp 45.000 (-25%). Pakai HEMAT10 untuk 10% lagi.', $promo->content['whatsapp']);
        $this->assertSame('HEMAT10', $promo->voucher_code);
        $this->assertSame([], $promo->warnings);
        $this->assertStringContainsString('{kode}', $writer->seen['user']);
    }

    public function test_an_unknown_or_package_only_voucher_is_refused(): void
    {
        $product = $this->product();
        Voucher::create(['code' => 'PAKETSAJA', 'type' => 'fixed', 'value' => 5000, 'is_active' => true, 'scope' => 'package']);

        foreach (['NGAWUR', 'PAKETSAJA'] as $code) {
            $this->actingAs($this->admin())->post('/dashboard/shop/promos', $this->payload(['product_id' => $product->id, 'voucher_code' => $code]))
                ->assertSessionHasErrors('promo');
        }
        $this->assertSame(0, PromoContent::count());
    }

    public function test_validation_and_a_failing_claude_call_save_nothing_and_say_why(): void
    {
        $product = $this->product();
        $admin = $this->admin();

        $this->actingAs($admin)->post('/dashboard/shop/promos', $this->payload(['product_id' => $product->id, 'formats' => []]))->assertSessionHasErrors('formats');
        $this->actingAs($admin)->post('/dashboard/shop/promos', $this->payload(['product_id' => $product->id, 'formats' => ['sms']]))->assertSessionHasErrors('formats.0');
        $this->actingAs($admin)->post('/dashboard/shop/promos', $this->payload(['product_id' => 'nope']))->assertSessionHasErrors('product_id');

        $this->app->instance(PromoWriter::class, new StubbedPromoWriter(new PromoException('Claude sedang sibuk.')));
        $this->actingAs($admin)->post('/dashboard/shop/promos', $this->payload(['product_id' => $product->id]))
            ->assertSessionHasErrors(['promo' => 'Claude sedang sibuk.']);

        $this->assertSame(0, PromoContent::count());
    }

    public function test_the_daily_limit_stops_further_generations(): void
    {
        config(['services.anthropic.promo_daily_limit' => 1]);
        $product = $this->product();
        $admin = $this->admin();

        $this->actingAs($admin)->post('/dashboard/shop/promos', $this->payload(['product_id' => $product->id]))->assertSessionHasNoErrors();
        $this->actingAs($admin)->post('/dashboard/shop/promos', $this->payload(['product_id' => $product->id]))->assertSessionHasErrors('promo');

        $this->assertSame(1, PromoContent::count());
    }

    public function test_editing_saves_the_text_marks_it_final_and_rechecks_the_warnings(): void
    {
        $this->app->instance(PromoWriter::class, new StubbedPromoWriter(['tagline' => 'Promo 70% hari ini', 'hashtags' => ['a']]));
        $product = $this->product();
        $admin = $this->admin();
        $this->actingAs($admin)->post('/dashboard/shop/promos', $this->payload(['product_id' => $product->id, 'formats' => ['tagline', 'hashtags']]));
        $promo = PromoContent::firstOrFail();
        $this->assertNotEmpty($promo->warnings);

        $this->actingAs($admin)->patch(route('dashboard.shop.promos.update', $promo), [
            'status' => 'final',
            'content' => ['tagline' => '  Praktis tiap hari  ', 'hashtags' => "#ebook\n\n#anak\n", 'banner' => 'ignored'],
        ])->assertSessionHas('success');

        $promo->refresh();
        $this->assertSame('Praktis tiap hari', $promo->content['tagline']);
        $this->assertSame(['#ebook', '#anak'], $promo->content['hashtags']);
        $this->assertArrayNotHasKey('banner', $promo->content);
        $this->assertSame('final', $promo->status);
        $this->assertSame([], $promo->warnings);
    }

    public function test_regenerate_makes_a_new_version_that_sees_the_old_text_and_the_instruction(): void
    {
        $product = $this->product();
        $admin = $this->admin();
        $this->actingAs($admin)->post('/dashboard/shop/promos', $this->payload(['product_id' => $product->id, 'formats' => ['tagline'], 'notes' => 'sasar ibu muda']));
        $first = PromoContent::firstOrFail();

        $this->actingAs($admin)->post(route('dashboard.shop.promos.regenerate', $first), ['instruction' => 'lebih singkat'])->assertRedirect();

        $this->assertSame(2, PromoContent::count());
        $this->assertStringContainsString('lebih singkat', $this->writer->seen['user']);
        $this->assertStringContainsString('Praktis tiap hari', $this->writer->seen['user'], 'the previous draft is sent along');
        $this->assertStringContainsString('sasar ibu muda', $this->writer->seen['user']);
        $this->assertSame('sasar ibu muda', PromoContent::where('id', '!=', $first->id)->first()->notes);
    }

    public function test_list_and_delete(): void
    {
        $product = $this->product();
        $admin = $this->admin();
        $this->actingAs($admin)->post('/dashboard/shop/promos', $this->payload(['product_id' => $product->id]));
        $promo = PromoContent::firstOrFail();

        $this->actingAs($admin)->get('/dashboard/shop/promos')->assertOk()->assertSee('Parenting Islami Praktis');
        $this->actingAs($admin)->get('/dashboard/shop/promos/create?product='.$product->id)->assertOk()->assertSee('Format konten');
        $this->actingAs($admin)->delete(route('dashboard.shop.promos.destroy', $promo))->assertRedirect(route('dashboard.shop.promos.index'));
        $this->assertSame(0, PromoContent::count());
    }

    public function test_a_missing_api_key_gives_a_clear_message_instead_of_calling_out(): void
    {
        config(['services.anthropic.key' => null]);

        $real = new PromoWriter();
        $this->expectException(PromoException::class);
        $this->expectExceptionMessage('ANTHROPIC_API_KEY');

        $real->generate($this->product()->load('variants'), ['locale' => 'id', 'goal' => 'launch', 'tone' => 'casual', 'formats' => ['tagline']]);
    }

    public function test_formats_registry_builds_matching_schema_and_form_round_trip(): void
    {
        $schema = PromoFormats::schemaFor(['email', 'facebook', 'ads', 'bogus']);

        $this->assertSame(['facebook', 'email', 'ads'], $schema['required']);
        $this->assertSame('array', $schema['properties']['ads']['type']);
        $this->assertSame(['subject', 'body'], $schema['properties']['email']['required']);
        $this->assertSame("a\nb", PromoFormats::forForm('ads', ['a', 'b']));
    }
}
