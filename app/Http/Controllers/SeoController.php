<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Package;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Support\Seo;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Response;

class SeoController extends Controller
{
    /** Paths no crawler should fetch: private areas, per-person pages and dev tools. */
    private const DISALLOW = [
        '/dashboard/', '/login', '/register', '/forgot-password', '/reset-password/', '/verify-email',
        '/confirm-password', '/checkout/', '/my-orders', '/my-websites', '/profile', '/invoice/',
        '/styleguide', '/switch-language/', '/cart', '/shop/checkout', '/shop/orders/', '/my-purchases',
        '/my-downloads', '/my-addresses',
    ];

    /**
     * Search and AI crawlers that are welcome. They are listed on purpose: a crawler that has its own
     * group ignores the "*" group, so each group repeats the same Disallow rules.
     */
    private const WELCOME = [
        'Googlebot', 'Bingbot',
        'OAI-SearchBot', 'ChatGPT-User', 'GPTBot',          // OpenAI: ChatGPT search, browsing, training
        'Claude-SearchBot', 'Claude-User', 'ClaudeBot',      // Anthropic
        'PerplexityBot', 'Perplexity-User',                  // Perplexity
        'Google-Extended', 'Applebot', 'Applebot-Extended',  // Gemini / Apple Intelligence
        'CCBot', 'Meta-ExternalAgent', 'Amazonbot', 'DuckAssistBot',
    ];

    public function sitemap()
    {
        $blogs = BlogPost::where('is_published', true)->orderByDesc('published_at')->get();
        $packages = Package::where('is_active', true)->whereNotNull('slug')->orderBy('order')->get();

        $latest = fn ($items) => $items->max('updated_at');

        $urls = [
            ['loc' => route('home'), 'lastmod' => collect([$latest($packages), $latest($blogs), Project::max('updated_at'), Service::max('updated_at')])->filter()->max()],
            ['loc' => route('package.index'), 'lastmod' => $latest($packages)],
            ['loc' => route('portfolio.index'), 'lastmod' => Project::max('updated_at')],
            ['loc' => route('blog.index'), 'lastmod' => $latest($blogs)],
        ];

        foreach ($packages as $package) {
            $urls[] = ['loc' => route('package.show', $package->slug), 'lastmod' => $package->updated_at];
        }

        foreach ($blogs as $blog) {
            $urls[] = ['loc' => route('blog.show', $blog->slug), 'lastmod' => $blog->updated_at ?? $blog->published_at];
        }

        $settings = SiteSetting::current();

        if ($settings->shop_enabled) {
            $products = Product::visibleInShop($settings)->orderByDesc('updated_at')->get(['slug', 'updated_at']);
            $urls[] = ['loc' => route('shop.index'), 'lastmod' => $latest($products)];

            foreach ($products as $product) {
                $urls[] = ['loc' => route('shop.show', $product->slug), 'lastmod' => $product->updated_at];
            }
        }

        $urls = array_map(fn ($u) => ['loc' => $u['loc'], 'lastmod' => $u['lastmod'] ? \Illuminate\Support\Carbon::parse($u['lastmod'])->toAtomString() : null], $urls);

        return Response::make(view('seo.sitemap', compact('urls'))->render(), 200, ['Content-Type' => 'application/xml']);
    }

    public function robots()
    {
        $rules = collect(self::DISALLOW)->map(fn ($p) => "Disallow: {$p}")->implode("\n");

        $content = "# Search engines and AI assistants are welcome on the public pages.\n";
        $content .= "# Private areas (dashboard, checkout, orders, invoices) are closed to everyone.\n\n";

        foreach (self::WELCOME as $agent) {
            $content .= "User-agent: {$agent}\nAllow: /\n{$rules}\n\n";
        }

        $content .= "User-agent: *\nAllow: /\n{$rules}\n\n";
        $content .= 'Sitemap: '.route('sitemap')."\n";

        return Response::make($content, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    /**
     * llms.txt: a plain Markdown summary of the business for AI assistants (https://llmstxt.org).
     * Built from the database so prices and articles never go stale.
     */
    public function llms()
    {
        App::setLocale('id');

        $site = SiteSetting::first() ?? new SiteSetting();
        $name = $site->site_name ?: Seo::BRAND;
        $packages = Package::where('is_active', true)->orderBy('order')->get();
        $services = Service::where('is_active', true)->orderBy('order')->get();
        $blogs = BlogPost::where('is_published', true)->orderByDesc('published_at')->limit(30)->get();
        $projects = Project::orderBy('order')->get();

        $out = "# {$name}\n\n";
        $out .= '> '.($site->seo_description ?: Seo::FALLBACK_DESCRIPTION)."\n\n";
        $out .= "{$name} adalah penyedia jasa pembuatan website, toko online, dan aplikasi untuk UMKM dan bisnis di Indonesia. ";
        $out .= "Pelanggan memilih paket di situs, membayar DP 50% atau langsung lunas lewat checkout, lalu memantau progres pengerjaan dan invoice dari akun mereka. ";
        $out .= "Situs ini berbahasa Indonesia.\n\n";

        if ($packages->isNotEmpty()) {
            $out .= "## Paket layanan\n\n";
            foreach ($packages as $p) {
                $desc = Seo::excerpt($p->getTranslation('description'), 200);
                $out .= '- ['.$p->getTranslation('name').']('.route('package.show', $p->slug).'): '.($p->price ?: 'harga sesuai kebutuhan').($desc ? " — {$desc}" : '')."\n";
            }
            $out .= '- [Semua paket]('.route('package.index').")\n\n";
        }

        if ($site->shop_enabled) {
            $products = Product::visibleInShop($site)->orderByDesc('is_featured')->orderBy('sort_order')->limit(30)->get();

            if ($products->isNotEmpty()) {
                $out .= "## Toko (produk digital)\n\n";
                foreach ($products as $product) {
                    $desc = Seo::excerpt($product->getTranslation('short_description') ?: $product->getTranslation('description'), 160);
                    $price = 'Rp '.number_format($product->min_price, 0, ',', '.').($product->hasPriceRange() ? ' – Rp '.number_format($product->max_price, 0, ',', '.') : '');
                    $out .= '- ['.$product->getTranslation('name').']('.route('shop.show', $product->slug).'): '.$price.($desc ? " — {$desc}" : '')."\n";
                }
                $out .= '- [Semua produk]('.route('shop.index').")\n\n";
            }
        }

        if ($services->isNotEmpty()) {
            $out .= "## Layanan\n\n";
            foreach ($services as $s) {
                $desc = Seo::excerpt($s->getTranslation('description'), 160);
                $out .= '- '.$s->getTranslation('title').($desc ? ": {$desc}" : '')."\n";
            }
            $out .= "\n";
        }

        if ($projects->isNotEmpty()) {
            $out .= "## Portofolio\n\n";
            foreach ($projects->take(15) as $pr) {
                $desc = Seo::excerpt($pr->getTranslation('description'), 140);
                $link = Seo::publicUrl($pr->url);
                $title = $pr->getTranslation('title');
                $out .= '- '.($link ? "[{$title}]({$link})" : $title).($desc ? ": {$desc}" : '')."\n";
            }
            $out .= '- [Lihat portofolio]('.route('portfolio.index').")\n\n";
        }

        if ($blogs->isNotEmpty()) {
            $out .= "## Artikel\n\n";
            foreach ($blogs as $b) {
                $out .= '- ['.$b->getTranslation('title').']('.route('blog.show', $b->slug).'): '.Seo::excerpt($b->getTranslation('content'), 140)."\n";
            }
            $out .= "\n";
        }

        $out .= "## Kontak\n\n";
        $out .= '- Situs: '.url('/')."\n";
        $place = collect([$site->biz_street, $site->biz_city, $site->biz_region])->filter()->implode(', ');
        if ($place) {
            $out .= "- Lokasi: {$place}".($site->biz_postal_code ? " {$site->biz_postal_code}" : '')."\n";
        }
        if ($site->biz_opening_hours) {
            $out .= "- Jam buka: {$site->biz_opening_hours}\n";
        }
        if ($site->contact_email) {
            $out .= "- Email: {$site->contact_email}\n";
        }
        if ($site->contact_phone) {
            $out .= "- WhatsApp: {$site->contact_phone}\n";
        }

        return Response::make($out, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
