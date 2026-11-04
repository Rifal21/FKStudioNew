<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogViewsTest extends TestCase
{
    use RefreshDatabase;

    private const PHONE = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 Mobile/15E148 Safari/604.1';

    private BlogPost $blog;

    protected function setUp(): void
    {
        parent::setUp();

        SiteSetting::create(['site_name' => 'FKStudio IT Solution']);
        $author = User::factory()->create();
        $this->blog = BlogPost::create(['author_id' => $author->id, 'slug' => 'artikel', 'title_id' => 'Artikel', 'title_en' => 'Article', 'content' => '<p>Isi.</p>', 'is_published' => true, 'published_at' => now()->subDay()]);
    }

    private function read(string $agent = self::PHONE, string $ip = '203.0.113.5', array $headers = [])
    {
        return $this->withHeaders($headers + ['User-Agent' => $agent])->withServerVariables(['REMOTE_ADDR' => $ip])->get('/blog/artikel')->assertOk();
    }

    public function test_a_reader_is_counted_once_per_day_however_often_they_refresh(): void
    {
        $this->read();
        $this->read();
        $this->read();

        $this->assertSame(1, $this->blog->fresh()->views);
        $this->assertDatabaseCount('blog_post_views', 1);
    }

    public function test_a_different_reader_adds_one(): void
    {
        $this->read(ip: '203.0.113.5');
        $this->read(ip: '203.0.113.6');

        $this->assertSame(2, $this->blog->fresh()->views);
    }

    public function test_the_same_reader_counts_again_the_next_day(): void
    {
        $this->read();
        $this->travel(1)->days();
        $this->read();

        $this->assertSame(2, $this->blog->fresh()->views);
    }

    public function test_crawlers_link_previews_and_prefetches_are_not_counted(): void
    {
        $this->read('Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)');
        $this->read('WhatsApp/2.23.20 A');
        $this->read('facebookexternalhit/1.1');
        $this->read('curl/8.4.0');
        $this->read('');
        $this->read(self::PHONE, headers: ['Sec-Purpose' => 'prefetch']);

        $this->assertSame(0, $this->blog->fresh()->views);
        $this->assertDatabaseCount('blog_post_views', 0);
    }

    public function test_admins_previewing_their_own_article_are_not_counted(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'super_admin']));

        $this->read();

        $this->assertSame(0, $this->blog->fresh()->views);
    }

    public function test_counting_leaves_updated_at_alone_for_the_sitemap(): void
    {
        $before = $this->blog->fresh()->updated_at;

        $this->travel(2)->hours();
        $this->read();

        $this->assertTrue($this->blog->fresh()->updated_at->equalTo($before));
    }

    public function test_admin_detail_page_shows_the_article_and_its_reader_numbers(): void
    {
        $this->read(ip: '203.0.113.5');
        $this->read(ip: '203.0.113.6');
        $this->actingAs(User::factory()->create(['role' => 'super_admin']));

        $this->get(route('dashboard.blogs.show', $this->blog->id))
            ->assertOk()
            ->assertSee('Artikel')
            ->assertSee('Isi.')
            ->assertSee('Total pembaca')
            ->assertSee('2 pembaca', false);
    }

    public function test_admin_detail_page_is_for_super_admins_only(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'user']));

        $this->get(route('dashboard.blogs.show', $this->blog->id))->assertForbidden();
    }

    public function test_the_blog_list_can_open_the_editor_for_one_article(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'super_admin']));

        $this->get(route('dashboard.blogs.index', ['edit' => $this->blog->id]))
            ->assertOk()
            ->assertSee('blog-edit', false);
        $this->get(route('dashboard.blogs.index'))->assertOk()->assertDontSee("new CustomEvent('blog-edit'", false);
    }
}
