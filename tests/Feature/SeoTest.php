<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Package;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\Seo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        SiteSetting::create(['site_name' => 'FKStudio', 'contact_email' => 'hi@fk.test', 'contact_phone' => '+62 812 0000 0000']);

        Package::create(['slug' => 'starter', 'name_id' => 'Starter', 'name_en' => 'Starter', 'description_en' => 'Starter plan', 'price' => 'Rp 7.500.000', 'description_id' => '<p>Paket awal.</p><p>Cocok untuk UMKM.</p>', 'features_id' => ['Desain custom', 'SSL'], 'is_active' => true]);
        Package::create(['slug' => 'hidden', 'name_id' => 'Hidden', 'name_en' => 'Hidden', 'price' => 'Rp 1', 'is_active' => false]);
        Package::create(['slug' => 'enterprise', 'name_id' => 'Enterprise', 'name_en' => 'Enterprise', 'price' => 'Hubungi kami', 'is_active' => true]);

        $author = User::factory()->create();

        BlogPost::create(['author_id' => $author->id, 'slug' => 'artikel-satu', 'title_id' => 'Artikel Satu', 'title_en' => 'Article One', 'content' => '<p>Isi artikel.</p>', 'author_name' => 'Rifal', 'is_published' => true, 'published_at' => now()->subDay()]);
        BlogPost::create(['author_id' => $author->id, 'slug' => 'draft', 'title_id' => 'Draft', 'title_en' => 'Draft', 'content' => 'x', 'is_published' => false]);
    }

    /** @return array<int, array> every JSON-LD block on the page, decoded (fails the test on invalid JSON) */
    private function jsonLd(string $html): array
    {
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m);

        return array_map(function ($json) {
            $data = json_decode($json, true);
            $this->assertNotNull($data, 'Invalid JSON-LD: '.json_last_error_msg());
            $this->assertSame('https://schema.org', $data['@context']);

            return $data;
        }, $m[1]);
    }

    public function test_robots_welcomes_ai_crawlers_and_closes_private_areas(): void
    {
        $robots = $this->get('/robots.txt')->assertOk()->getContent();

        foreach (['GPTBot', 'OAI-SearchBot', 'ClaudeBot', 'PerplexityBot', 'Google-Extended'] as $agent) {
            $this->assertStringContainsString("User-agent: {$agent}\nAllow: /", $robots);
        }
        // Every group repeats the private paths, because a named group ignores the "*" group.
        $this->assertSame(substr_count($robots, 'User-agent:'), substr_count($robots, 'Disallow: /invoice/'));
        $this->assertSame(substr_count($robots, 'User-agent:'), substr_count($robots, 'Disallow: /dashboard/'));
        $this->assertStringContainsString('Sitemap: '.route('sitemap'), $robots);
        $this->assertFileDoesNotExist(public_path('robots.txt'), 'a static robots.txt would hide the dynamic one');
    }

    public function test_sitemap_lists_public_pages_and_published_content_only(): void
    {
        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();

        foreach ([route('home'), route('package.index'), route('portfolio.index'), route('blog.index'), route('package.show', 'starter'), route('blog.show', 'artikel-satu')] as $url) {
            $this->assertStringContainsString("<loc>{$url}</loc>", $xml);
        }
        $this->assertStringNotContainsString('hidden', $xml);
        $this->assertStringNotContainsString('/blog/draft', $xml);
        $this->assertStringNotContainsString('/checkout', $xml);
        $this->assertStringNotContainsString('/invoice', $xml);
    }

    public function test_llms_txt_summarises_the_business_with_current_prices(): void
    {
        $txt = $this->get('/llms.txt')->assertOk()->getContent();

        $this->assertStringStartsWith('# FKStudio', $txt);
        $this->assertStringContainsString('Rp 7.500.000', $txt);
        $this->assertStringContainsString(route('package.show', 'starter'), $txt);
        $this->assertStringContainsString(route('blog.show', 'artikel-satu'), $txt);
        $this->assertStringContainsString('hi@fk.test', $txt);
        $this->assertStringNotContainsString('Hidden', $txt);
        $this->assertStringNotContainsString('Draft', $txt);
    }

    public function test_package_page_has_a_product_with_a_numeric_idr_offer(): void
    {
        $blocks = $this->jsonLd($this->get('/packages/starter')->assertOk()->getContent());
        $product = collect($blocks)->pluck('@graph')->flatten(1)->firstWhere('@type', 'Product');

        $this->assertSame('Starter', $product['name']);
        $this->assertSame(7500000, $product['offers']['price']);
        $this->assertSame('IDR', $product['offers']['priceCurrency']);
        $this->assertSame('Paket awal. Cocok untuk UMKM.', $product['description']);
    }

    public function test_a_package_priced_by_quote_has_no_fake_offer(): void
    {
        $this->assertNull(Seo::product(Package::where('slug', 'enterprise')->first())['offers'] ?? null);
    }

    public function test_blog_post_has_blogposting_and_breadcrumbs_and_article_meta(): void
    {
        $html = $this->get('/blog/artikel-satu')->assertOk()->getContent();
        $graph = collect($this->jsonLd($html))->pluck('@graph')->flatten(1);

        $this->assertSame('Artikel Satu', $graph->firstWhere('@type', 'BlogPosting')['headline']);
        $this->assertCount(3, $graph->firstWhere('@type', 'BreadcrumbList')['itemListElement']);
        $this->assertStringContainsString('<meta property="og:type" content="article">', $html);
        $this->assertStringContainsString('article:published_time', $html);
    }

    public function test_reading_a_post_does_not_change_its_modified_date(): void
    {
        $post = BlogPost::where('slug', 'artikel-satu')->first();
        $before = $post->updated_at;

        $this->travel(2)->days();
        $this->get('/blog/artikel-satu')->assertOk();

        $post->refresh();
        $this->assertSame(1, (int) $post->views);
        $this->assertEquals($before, $post->updated_at);
    }

    public function test_public_pages_are_indexable_and_private_ones_are_not(): void
    {
        $this->get('/products')->assertSee('content="index, follow', false);
        $this->get('/register')->assertSee('<meta name="robots" content="noindex, nofollow">', false);
        $this->get('/login')->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    public function test_json_ld_cannot_be_broken_out_of_by_content(): void
    {
        BlogPost::create(['author_id' => User::first()->id, 'slug' => 'xss', 'title_id' => 'Judul </script><script>alert(1)</script>', 'title_en' => 'x', 'content' => 'x', 'is_published' => true, 'published_at' => now()]);

        $html = $this->get('/blog/xss')->assertOk()->getContent();

        $this->jsonLd($html);
        $this->assertStringNotContainsString('</script><script>alert(1)', $html);
    }
}
