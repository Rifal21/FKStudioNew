<?php

namespace App\Support;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Counts readers of a blog post. A view is counted once per visitor, per article, per day, and only when it
 * looks like a person reading: crawlers, link-preview fetchers, browser prefetches and our own staff are skipped.
 */
class BlogViewTracker
{
    /** User-agent fragments of crawlers and of the bots that fetch a page to build a link preview. */
    private const BOT_PATTERN = '/bot|crawl|spider|slurp|scrape|fetch|preview|headless|lighthouse|pingdom|uptime|monitor|curl|wget|python-requests|go-http|okhttp|axios|java\/|libwww|facebookexternalhit|whatsapp|telegram|discord|slack|embedly|quora|pinterest|bingpreview|yandex|baidu|duckduck|ahrefs|semrush|mj12|dotbot|petal|gptbot|claude|perplexity|bytespider/i';

    /** Records the view and returns true when it was a new reader (and so was counted). */
    public static function record(BlogPost $blog, Request $request): bool
    {
        if (! self::countable($request)) {
            return false;
        }

        $today = now()->toDateString();

        // insertOrIgnore reports 0 rows when this visitor already read the article today.
        $isNew = DB::table('blog_post_views')->insertOrIgnore([
            'blog_post_id' => $blog->getKey(),
            'visitor_hash' => self::visitorHash($request),
            'viewed_on' => $today,
            'created_at' => now(),
        ]) === 1;

        if ($isNew) {
            // Counting must not touch updated_at: it feeds the sitemap lastmod and the article's dateModified,
            // which should only move when the article itself changes.
            $blog->timestamps = false;
            $blog->increment('views');
            $blog->timestamps = true;
        }

        return $isNew;
    }

    public static function countable(Request $request): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }

        // Browsers warming a page the visitor has not opened (yet) send one of these headers.
        $purpose = strtolower($request->header('Sec-Purpose', $request->header('Purpose', '')));
        if (str_contains($purpose, 'prefetch') || str_contains($purpose, 'prerender') || $request->header('X-Moz') === 'prefetch') {
            return false;
        }

        $agent = (string) $request->userAgent();
        if ($agent === '' || preg_match(self::BOT_PATTERN, $agent)) {
            return false;
        }

        // Previewing an article from the admin must not inflate its numbers.
        return ! $request->user()?->isSuperAdmin();
    }

    private static function visitorHash(Request $request): string
    {
        return hash_hmac('sha256', $request->ip().'|'.$request->userAgent(), (string) config('app.key'));
    }
}
