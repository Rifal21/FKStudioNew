<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\User;
use App\Support\OgImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\Feature\Shop\BuildsShop;
use Tests\TestCase;

/** What WhatsApp, Facebook, LinkedIn and X read when a link is pasted. */
class ShareCardTest extends TestCase
{
    use BuildsShop, RefreshDatabase;

    private const IMAGE = 'https://cdn.example.test/cover.jpg';

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        $this->shopSettings();

        ob_start();
        imagejpeg(imagecreatetruecolor(1200, 630));
        Http::fake([self::IMAGE => Http::response(ob_get_clean(), 200, ['Content-Type' => 'image/jpeg'])]);
    }

    private function meta(string $html, string $property): ?string
    {
        preg_match('#<meta (?:property|name)="'.preg_quote($property, '#').'" content="([^"]*)"#', $html, $m);

        return isset($m[1]) ? html_entity_decode($m[1]) : null;
    }

    private function blog(array $attributes = []): BlogPost
    {
        return BlogPost::create($attributes + [
            'author_id' => User::factory()->create()->id,
            'slug' => 'artikel',
            'title_id' => 'Cara Memilih Jasa Website',
            'title_en' => 'How to choose a web agency',
            'content' => '<p>'.str_repeat('Kalimat panjang tentang website untuk bisnis kecil. ', 12).'Akhir.</p>',
            'image' => self::IMAGE,
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);
    }

    public function test_blog_share_card_has_image_size_alt_and_a_clean_title(): void
    {
        $this->blog();

        $html = $this->get('/blog/artikel')->assertOk()->getContent();

        $this->assertSame('Cara Memilih Jasa Website', $this->meta($html, 'og:title'));
        $this->assertSame('Cara Memilih Jasa Website', $this->meta($html, 'twitter:title'));
        $this->assertSame(self::IMAGE, $this->meta($html, 'og:image'));
        $this->assertSame('image/jpeg', $this->meta($html, 'og:image:type'));
        $this->assertSame('1200', $this->meta($html, 'og:image:width'));
        $this->assertSame('630', $this->meta($html, 'og:image:height'));
        $this->assertSame('Cara Memilih Jasa Website', $this->meta($html, 'og:image:alt'));
        $this->assertSame('summary_large_image', $this->meta($html, 'twitter:card'));
        $this->assertStringContainsString('Cara Memilih Jasa Website | FKStudio', $html, 'the browser <title> keeps the brand');
    }

    public function test_blog_description_ends_on_a_sentence_or_word_not_mid_word(): void
    {
        $this->blog(['content' => '<p>'.str_repeat('Website cepat membantu bisnis tumbuh. ', 10).'</p>']);

        $description = $this->meta($this->get('/blog/artikel')->getContent(), 'og:description');

        $this->assertLessThanOrEqual(156, mb_strlen($description));
        $this->assertMatchesRegularExpression('/[.!?…]$/u', $description);
    }

    public function test_product_share_card_is_a_product_with_price(): void
    {
        $product = $this->digitalProduct(['name_id' => 'Template Toko', 'short_description_id' => 'Siap pakai.'], 45000);
        $product->images()->create(['path' => self::IMAGE, 'sort_order' => 0]);

        $html = $this->get(route('shop.show', $product->slug))->assertOk()->getContent();

        $this->assertSame('product', $this->meta($html, 'og:type'));
        $this->assertSame('45000', $this->meta($html, 'product:price:amount'));
        $this->assertSame('IDR', $this->meta($html, 'product:price:currency'));
        $this->assertSame('in stock', $this->meta($html, 'product:availability'));
        $this->assertSame(self::IMAGE, $this->meta($html, 'og:image'));
        $this->assertSame('1200', $this->meta($html, 'og:image:width'));
        $this->assertSame('Template Toko', $this->meta($html, 'og:image:alt'));
    }

    public function test_page_without_any_image_falls_back_to_a_small_card_without_image_tags(): void
    {
        $html = $this->get('/blog')->assertOk()->getContent();

        $this->assertNull($this->meta($html, 'og:image'));
        $this->assertNull($this->meta($html, 'og:image:width'));
        $this->assertSame('summary', $this->meta($html, 'twitter:card'));
    }

    public function test_unreachable_image_still_renders_the_card_without_size(): void
    {
        $broken = 'https://cdn.example.test/broken.jpg';
        Http::fake([$broken => Http::response('', 500)]);
        $this->blog(['image' => $broken]);

        $html = $this->get('/blog/artikel')->assertOk()->getContent();

        $this->assertSame($broken, $this->meta($html, 'og:image'));
        $this->assertSame('image/jpeg', $this->meta($html, 'og:image:type'));
        $this->assertNull($this->meta($html, 'og:image:width'));
    }

    public function test_og_upload_is_cropped_to_1200x630_and_stays_under_300kb(): void
    {
        // Noisy 2000x2000 PNG: large on disk and not 1.91:1.
        $img = imagecreatetruecolor(2000, 2000);
        for ($i = 0; $i < 20000; $i++) {
            imagesetpixel($img, random_int(0, 1999), random_int(0, 1999), random_int(0, 0xFFFFFF));
        }
        $path = tempnam(sys_get_temp_dir(), 'src');
        imagepng($img, $path, 0);

        $out = OgImage::prepare(new UploadedFile($path, 'big.png', 'image/png', null, true));

        [$w, $h, $type] = getimagesize($out->getRealPath());
        $this->assertSame([1200, 630, IMAGETYPE_JPEG], [$w, $h, $type]);
        $this->assertLessThanOrEqual(OgImage::MAX_BYTES, filesize($out->getRealPath()));
        $this->assertSame('jpg', $out->getClientOriginalExtension());

        @unlink($path);
        @unlink($out->getRealPath());
    }

    public function test_og_upload_that_cannot_be_decoded_is_kept_as_is(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'svg');
        file_put_contents($path, '<svg xmlns="http://www.w3.org/2000/svg"/>');
        $file = new UploadedFile($path, 'logo.svg', 'image/svg+xml', null, true);

        $this->assertSame($file, OgImage::prepare($file));

        @unlink($path);
    }
}
