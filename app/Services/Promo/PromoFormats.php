<?php

namespace App\Services\Promo;

/**
 * The pieces of promo copy the admin can ask for. Each one knows its JSON schema (what Claude must return),
 * its brief for the prompt, and how an edited form value goes back into the stored content.
 */
class PromoFormats
{
    public const INSTAGRAM = 'instagram';
    public const FACEBOOK = 'facebook';
    public const WHATSAPP = 'whatsapp';
    public const BANNER = 'banner';
    public const EMAIL = 'email';
    public const ADS = 'ads';
    public const TAGLINE = 'tagline';
    public const HASHTAGS = 'hashtags';

    /** @return array<string, array{label: string, icon: string, brief: string, schema: array, kind: string}> */
    public static function all(): array
    {
        $string = ['type' => 'string'];
        $strings = ['type' => 'array', 'items' => ['type' => 'string']];

        return [
            self::INSTAGRAM => [
                'label' => 'Caption Instagram',
                'icon' => 'fa-brands fa-instagram',
                'kind' => 'text',
                'brief' => 'Instagram caption, at most about 900 characters. First line is a hook. Short paragraphs, at most 3 emojis, a clear call to action that ends with {link}. No hashtags here (they have their own field).',
                'schema' => $string,
            ],
            self::FACEBOOK => [
                'label' => 'Postingan Facebook',
                'icon' => 'fa-brands fa-facebook',
                'kind' => 'text',
                'brief' => 'Facebook post of 2 to 3 short paragraphs, at most about 700 characters. Friendly and a little more explanatory than Instagram; open with a relatable question or situation, say what the reader gets, and put {link} on its own line near the end so Facebook shows the link preview. At most 2 hashtags, only at the very end (or none).',
                'schema' => $string,
            ],
            self::WHATSAPP => [
                'label' => 'Teks WhatsApp',
                'icon' => 'fa-brands fa-whatsapp',
                'kind' => 'text',
                'brief' => 'WhatsApp message for a status or a broadcast, at most about 600 characters. Conversational. WhatsApp markup is fine (*bold*). Ends with {link}.',
                'schema' => $string,
            ],
            self::BANNER => [
                'label' => 'Banner (headline)',
                'icon' => 'fa-panorama',
                'kind' => 'pair',
                'brief' => 'Banner copy: "headline" of at most 8 words and "subheadline" of at most 20 words. No link.',
                'schema' => [
                    'type' => 'object',
                    'properties' => ['headline' => $string, 'subheadline' => $string],
                    'required' => ['headline', 'subheadline'],
                    'additionalProperties' => false,
                ],
            ],
            self::EMAIL => [
                'label' => 'Email',
                'icon' => 'fa-envelope',
                'kind' => 'pair',
                'brief' => 'Promo email: "subject" of at most 60 characters and "body" of 80 to 150 words in short paragraphs, ending with a call to action that includes {link}.',
                'schema' => [
                    'type' => 'object',
                    'properties' => ['subject' => $string, 'body' => $string],
                    'required' => ['subject', 'body'],
                    'additionalProperties' => false,
                ],
            ],
            self::ADS => [
                'label' => 'Iklan pendek (3 variasi)',
                'icon' => 'fa-bullhorn',
                'kind' => 'lines',
                'brief' => 'Exactly 3 different short ad lines, each at most 90 characters, each with a different angle (benefit, problem, curiosity).',
                'schema' => $strings,
            ],
            self::TAGLINE => [
                'label' => 'Tagline',
                'icon' => 'fa-quote-left',
                'kind' => 'text',
                'brief' => 'One tagline of at most 8 words.',
                'schema' => $string,
            ],
            self::HASHTAGS => [
                'label' => 'Hashtag',
                'icon' => 'fa-hashtag',
                'kind' => 'lines',
                'brief' => '6 to 10 relevant hashtags, each starting with "#", no spaces inside a hashtag.',
                'schema' => $strings,
            ],
        ];
    }

    /** @return list<string> */
    public static function keys(): array
    {
        return array_keys(self::all());
    }

    /** JSON schema for the chosen formats only: Claude is not asked for (or billed for) the rest. */
    public static function schemaFor(array $formats): array
    {
        $all = self::all();
        $formats = array_values(array_filter(self::keys(), fn ($key) => in_array($key, $formats, true)));

        return [
            'type' => 'object',
            'properties' => collect($formats)->mapWithKeys(fn ($key) => [$key => $all[$key]['schema']])->all(),
            'required' => $formats,
            'additionalProperties' => false,
        ];
    }

    /** Turns what the edit form posted into the stored shape, dropping anything unknown. */
    public static function fromForm(array $input, array $formats): array
    {
        $all = self::all();
        $out = [];

        foreach ($formats as $key) {
            if (! isset($all[$key])) {
                continue;
            }

            $value = $input[$key] ?? null;

            $out[$key] = match ($all[$key]['kind']) {
                'pair' => collect(array_keys($all[$key]['schema']['properties']))
                    ->mapWithKeys(fn ($field) => [$field => trim((string) (is_array($value) ? ($value[$field] ?? '') : ''))])->all(),
                'lines' => collect(preg_split('/\R+/', is_array($value) ? '' : (string) $value))
                    ->map(fn ($line) => trim($line))->filter()->values()->all(),
                default => trim(is_array($value) ? '' : (string) $value),
            };
        }

        return $out;
    }

    /** One text value as the edit form shows it: list formats become one line per item. */
    public static function forForm(string $key, mixed $value): mixed
    {
        return self::all()[$key]['kind'] === 'lines' ? implode("\n", (array) $value) : $value;
    }
}
