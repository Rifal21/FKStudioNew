<?php

namespace App\Support;

use App\Models\BlogPost;
use App\Models\Package;
use App\Models\SiteSetting;
use Illuminate\Support\Str;

class Seo
{
    /** Used when the admin has not written an SEO description yet. */
    public const FALLBACK_DESCRIPTION = 'FKStudio membuat website dan aplikasi untuk UMKM dan bisnis di Indonesia. Harga paket transparan, bayar DP atau lunas, progres bisa dipantau online.';

    public static function orgId(): string
    {
        return rtrim(url('/'), '/').'/#organization';
    }

    public static function siteId(): string
    {
        return rtrim(url('/'), '/').'/#website';
    }

    /**
     * Encodes one JSON-LD document. Done here, not in a Blade file, because Blade would treat a literal
     * "@context" as a directive. HEX_TAG keeps "</script>" inside user content from closing the tag early.
     */
    public static function jsonLd(array $data): string
    {
        return json_encode(['@context' => 'https://schema.org'] + $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
    }

    /** "Rp 7.500.000" -> 7500000. Null when the price is text such as "Hubungi kami". */
    public static function price(?string $price): ?int
    {
        $digits = preg_replace('/\D+/', '', (string) $price);

        return $digits === '' ? null : (int) $digits;
    }

    public static function excerpt(?string $html, int $limit = 160): string
    {
        $text = strip_tags(preg_replace('#</(p|div|li|h[1-6])>|<br\s*/?>#i', ' ', (string) $html));

        return Str::limit(trim(preg_replace('/\s+/', ' ', $text)), $limit, '');
    }

    public static function organization(SiteSetting $site): array
    {
        $social = is_array($site->social_links) ? array_values(array_filter($site->social_links, 'is_string')) : [];

        // With a real street address the business is also a local one (Google Maps, "near me").
        // Without it we stay a plain Organization and publish no invented location.
        $local = filled($site->biz_street) && filled($site->biz_city);

        return array_filter([
            '@type' => $local ? ['Organization', 'ProfessionalService'] : 'Organization',
            '@id' => self::orgId(),
            'name' => $site->site_name ?: 'FKStudio',
            'url' => url('/'),
            'logo' => $site->site_logo ? $site->logo_url : null,
            'description' => $site->seo_description ?: self::FALLBACK_DESCRIPTION,
            'email' => $site->contact_email,
            'telephone' => $site->contact_phone,
            'areaServed' => 'ID',
            'address' => $local ? array_filter([
                '@type' => 'PostalAddress',
                'streetAddress' => $site->biz_street,
                'addressLocality' => $site->biz_city,
                'addressRegion' => $site->biz_region,
                'postalCode' => $site->biz_postal_code,
                'addressCountry' => 'ID',
            ]) : null,
            'openingHours' => $local && filled($site->biz_opening_hours) ? $site->biz_opening_hours : null,
            'priceRange' => filled($site->biz_price_range) ? $site->biz_price_range : null,
            'sameAs' => $social ?: null,
            'contactPoint' => array_filter([
                '@type' => 'ContactPoint',
                'telephone' => $site->contact_phone,
                'email' => $site->contact_email,
                'contactType' => 'customer service',
                'availableLanguage' => ['id', 'en'],
            ]),
        ]);
    }

    /** @param  iterable<\App\Models\Service>  $services */
    public static function services(iterable $services): array
    {
        $items = [];

        foreach ($services as $i => $service) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => count($items) + 1,
                'item' => array_filter([
                    '@type' => 'Service',
                    'name' => $service->getTranslation('title'),
                    'description' => self::excerpt($service->getTranslation('description'), 250) ?: null,
                    'provider' => ['@id' => self::orgId()],
                    'areaServed' => 'ID',
                ]),
            ];
        }

        return ['@type' => 'ItemList', 'name' => 'Layanan FKStudio', 'itemListElement' => $items];
    }

    public static function website(SiteSetting $site): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => self::siteId(),
            'url' => url('/'),
            'name' => $site->site_name ?: 'FKStudio',
            'inLanguage' => 'id-ID',
            'publisher' => ['@id' => self::orgId()],
        ];
    }

    public static function product(Package $package): array
    {
        $price = self::price($package->price);
        $features = $package->features_id ?? [];
        $features = is_array($features) ? $features : [];

        return array_filter([
            '@type' => 'Product',
            'name' => $package->getTranslation('name'),
            'description' => self::excerpt($package->getTranslation('description'), 300) ?: null,
            'url' => route('package.show', $package->slug),
            'brand' => ['@type' => 'Brand', 'name' => 'FKStudio'],
            'category' => 'Website & digital services',
            'additionalProperty' => $features ? array_map(fn ($f) => ['@type' => 'PropertyValue', 'name' => 'Fitur', 'value' => (string) (is_array($f) ? ($f['name'] ?? json_encode($f)) : $f)], array_slice($features, 0, 12)) : null,
            'offers' => $price === null ? null : [
                '@type' => 'Offer',
                'price' => $price,
                'priceCurrency' => 'IDR',
                'availability' => 'https://schema.org/InStock',
                'url' => route('package.show', $package->slug),
                'seller' => ['@id' => self::orgId()],
            ],
        ]);
    }

    public static function blogPosting(BlogPost $blog, SiteSetting $site, string $title, ?string $image): array
    {
        return array_filter([
            '@type' => 'BlogPosting',
            'headline' => $title,
            'description' => self::excerpt($blog->getTranslation('content'), 200),
            'image' => $image,
            'datePublished' => optional($blog->published_at ?? $blog->created_at)->toAtomString(),
            'dateModified' => optional($blog->updated_at)->toAtomString(),
            'inLanguage' => 'id-ID',
            'mainEntityOfPage' => route('blog.show', $blog->slug),
            'author' => ['@type' => 'Person', 'name' => $blog->author_name ?: $blog->author?->name ?: ($site->site_name ?: 'FKStudio')],
            'publisher' => ['@id' => self::orgId()],
        ]);
    }

    /** @param  array<int, array{0: string, 1: string}>  $items  [name, url] pairs, home first */
    public static function breadcrumbs(array $items): array
    {
        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_map(fn ($item, $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $item[0],
                'item' => $item[1],
            ], $items, array_keys($items)),
        ];
    }
}
