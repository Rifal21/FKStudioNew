<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\ProductCategory;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\Feature\Shop\BuildsShop;
use Tests\TestCase;

/** What Google Search Console reports on: duplicates, blocked-but-indexed pages, sitemap, structured data. */
class SearchConsoleTest extends TestCase
{
    use BuildsShop, RefreshDatabase;

    private const IMAGE = 'https://cdn.example.test/cover.jpg';

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        Http::fake();
        config(['app.url' => 'https://fkstudio.test']);
        $this->shopSettings();
    }

    private function canonical(string $html): ?string
    {
        preg_match('#<link rel="canonical" href="([^"]*)"#', $html, $m);

        return isset($m[1]) ? html_entity_decode($m[1]) : null;
    }

    private function robotsMeta(string $html): ?string
    {
        preg_match('#<meta name="robots" content="([^"]*)"#', $html, $m);

        return $m[1] ?? null;
    }

    public function test_www_is_redirected_to_the_bare_domain_keeping_path_and_query(): void
    {
        $this->get('http://www.fkstudio.test/shop?category=ebook')
            ->assertStatus(301)
            ->assertRedirect('https://fkstudio.test/shop?category=ebook');
    }

    /** The test client trims a trailing slash itself, so the raw request is built by hand. */
    private function throughMiddleware(string $url): \Symfony\Component\HttpFoundation\Response
    {
        return (new \App\Http\Middleware\CanonicalUrl)->handle(\Illuminate\Http\Request::create($url), fn () => response('passed through', 200));
    }

    public function test_trailing_slash_is_removed_with_a_permanent_redirect(): void
    {
        $res = $this->throughMiddleware('https://fkstudio.test/blog/');
        $this->assertSame(301, $res->getStatusCode());
        $this->assertSame('https://fkstudio.test/blog', $res->headers->get('Location'));

        $res = $this->throughMiddleware('https://fkstudio.test/blog/?page=2');
        $this->assertSame('https://fkstudio.test/blog?page=2', $res->headers->get('Location'));
    }

    public function test_plain_http_behind_the_proxy_is_sent_to_https_but_no_header_means_no_redirect(): void
    {
        $this->get('http://fkstudio.test/blog', ['X-Forwarded-Proto' => 'http'])
            ->assertStatus(301)
            ->assertRedirect('https://fkstudio.test/blog');

        $this->get('http://fkstudio.test/blog')->assertOk();
    }

    public function test_the_canonical_address_and_the_home_page_are_left_alone(): void
    {
        foreach (['https://fkstudio.test/', 'https://fkstudio.test/blog', 'https://fkstudio.test/blog?page=2', 'https://fkstudio.test/sitemap.xml'] as $url) {
            $this->assertSame(200, $this->throughMiddleware($url)->getStatusCode(), $url);
        }
        $this->get('https://fkstudio.test/blog', ['X-Forwarded-Proto' => 'https'])->assertOk();
    }

    public function test_robots_lets_google_read_noindex_on_linked_pages_but_still_closes_private_areas(): void
    {
        $robots = $this->get('/robots.txt')->assertOk();
        $body = $robots->getContent();

        foreach (['/login', '/register', '/forgot-password', '/cart'] as $linked) {
            $this->assertStringNotContainsString("Disallow: {$linked}\n", $body);
        }
        foreach (['/dashboard/', '/invoice/', '/my-orders', '/shop/checkout'] as $private) {
            $this->assertStringContainsString("Disallow: {$private}\n", $body);
        }
        $this->assertStringContainsString('max-age=3600', $robots->headers->get('Cache-Control'));

        // The pages that are now crawlable must say noindex themselves.
        $this->assertStringContainsString('noindex', $this->robotsMeta($this->get('/login')->getContent()));
        $this->assertStringContainsString('noindex', $this->robotsMeta($this->get('/register')->getContent()));
    }

    public function test_shop_category_and_page_get_their_own_canonical_while_sort_and_type_do_not(): void
    {
        $category = ProductCategory::create(['slug' => 'ebook', 'name_id' => 'E-book', 'name_en' => 'E-books', 'is_active' => true]);
        $this->digitalProduct(['product_category_id' => $category->id]);

        $shop = 'https://fkstudio.test/shop';

        $this->assertSame($shop, $this->canonical($this->get('https://fkstudio.test/shop')->getContent()));
        $this->assertSame($shop.'?category=ebook', $this->canonical($this->get('https://fkstudio.test/shop?category=ebook')->getContent()));
        $this->assertSame($shop.'?category=ebook', $this->canonical($this->get('https://fkstudio.test/shop?category=ebook&sort=price_asc&type=digital')->getContent()));
        $this->assertSame($shop, $this->canonical($this->get('https://fkstudio.test/shop?sort=newest&utm_source=wa')->getContent()));
        // An unknown category must not mint a canonical of its own.
        $this->assertSame($shop, $this->canonical($this->get('https://fkstudio.test/shop?category=nope')->getContent()));
    }

    public function test_shop_search_results_are_noindex_but_followed(): void
    {
        $this->digitalProduct();

        $this->assertStringStartsWith('index, follow', $this->robotsMeta($this->get('https://fkstudio.test/shop')->getContent()));
        $this->assertSame('noindex, follow', $this->robotsMeta($this->get('https://fkstudio.test/shop?q=template')->getContent()));
    }

    public function test_sitemap_has_images_and_category_pages(): void
    {
        $category = ProductCategory::create(['slug' => 'ebook', 'name_id' => 'E-book', 'is_active' => true]);
        $product = $this->digitalProduct(['product_category_id' => $category->id]);
        $product->images()->create(['path' => self::IMAGE, 'sort_order' => 0]);
        BlogPost::create(['author_id' => User::factory()->create()->id, 'slug' => 'a', 'title_id' => 'A', 'title_en' => 'A', 'content' => 'x', 'image' => 'https://cdn.example.test/blog.jpg', 'is_published' => true, 'published_at' => now()]);

        $res = $this->get('/sitemap.xml')->assertOk();
        $xml = $res->getContent();

        $this->assertStringContainsString('xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"', $xml);
        $this->assertStringContainsString('<image:loc>'.self::IMAGE.'</image:loc>', $xml);
        $this->assertStringContainsString('<image:loc>https://cdn.example.test/blog.jpg</image:loc>', $xml);
        $this->assertStringContainsString('<loc>'.route('shop.index', ['category' => 'ebook']).'</loc>', $xml);
        $this->assertNotNull(simplexml_load_string($xml), 'sitemap must be valid XML');
        $this->assertStringContainsString('max-age=3600', $res->headers->get('Cache-Control'));
    }

    public function test_blogposting_without_a_cover_falls_back_to_the_site_image(): void
    {
        SiteSetting::first()->update(['og_image' => 'https://cdn.example.test/og.jpg']);
        BlogPost::create(['author_id' => User::factory()->create()->id, 'slug' => 'tanpa-gambar', 'title_id' => 'Tanpa Gambar', 'title_en' => 'No image', 'content' => 'x', 'is_published' => true, 'published_at' => now()]);

        $html = $this->get('/blog/tanpa-gambar')->assertOk()->getContent();
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m);
        $graph = collect($m[1])->map(fn ($j) => json_decode($j, true))->pluck('@graph')->flatten(1);

        $this->assertSame('https://cdn.example.test/og.jpg', $graph->firstWhere('@type', 'BlogPosting')['image']);
    }

    public function test_product_offer_declares_a_new_item(): void
    {
        $product = $this->digitalProduct();

        $html = $this->get(route('shop.show', $product->slug))->assertOk()->getContent();
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m);
        $graph = collect($m[1])->map(fn ($j) => json_decode($j, true))->pluck('@graph')->flatten(1);

        $this->assertSame('https://schema.org/NewCondition', $graph->firstWhere('@type', 'Product')['offers']['itemCondition']);
    }
}
