<?php
declare(strict_types=1);

namespace ci4seopro\Libraries\Seo\Geo;

class GeoSchemaPreset
{
    public static function faqPage(array $faqs): array
    {
        return [
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => array_values(array_map(static fn($faq) => [
                '@type'          => 'Question',
                'name'           => $faq['q'] ?? '',
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['a'] ?? ''],
            ], $faqs)),
        ];
    }

    public static function howTo(array $d): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type'    => 'HowTo',
            'name'     => $d['name'] ?? '',
            'step'     => array_values(array_map(static fn($step) => [
                '@type' => 'HowToStep',
                'name'  => $step['name'] ?? '',
                'text'  => $step['text'] ?? '',
            ], $d['steps'] ?? [])),
        ];
    }

    public static function claimReview(array $d): array
    {
        return [
            '@context'      => 'https://schema.org',
            '@type'         => 'ClaimReview',
            'url'           => $d['url'] ?? '',
            'claimReviewed' => $d['claim'] ?? '',
            'reviewRating'  => ['@type' => 'Rating', 'ratingValue' => $d['rating'] ?? ''],
            'author'        => ['@type' => 'Organization', 'name' => $d['author'] ?? ''],
        ];
    }

    public static function speakable(string $url, array $selectors): array
    {
        return [
            '@context'  => 'https://schema.org',
            '@type'     => 'WebPage',
            'url'       => $url,
            'speakable' => [
                '@type'       => 'SpeakableSpecification',
                'cssSelector' => $selectors,
            ],
        ];
    }
}
