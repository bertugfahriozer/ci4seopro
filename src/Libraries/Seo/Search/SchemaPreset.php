<?php

namespace ci4seopro\Libraries\Seo\Search;

class SchemaPreset
{
    public static function article(array $d): array
    {
        return ['@context' => 'https://schema.org', '@type' => 'Article', 'headline' => $d['title'] ?? '', 'datePublished' => $d['date'] ?? null, 'author' => ['@type' => 'Person', 'name' => $d['author'] ?? ''], 'image' => $d['image'] ?? null, 'mainEntityOfPage' => $d['url'] ?? null, 'articleSection' => $d['section'] ?? null];
    }
    public static function product(array $d): array
    {
        $o = $d['offers'] ?? [];
        return ['@context' => 'https://schema.org', '@type' => 'Product', 'name' => $d['name'] ?? '', 'image' => $d['image'] ?? null, 'description' => $d['desc'] ?? '', 'sku' => $d['sku'] ?? null, 'brand' => ['@type' => 'Brand', 'name' => $d['brand'] ?? ''], 'offers' => ['@type' => 'Offer', 'priceCurrency' => $o['currency'] ?? 'TRY', 'price' => $o['price'] ?? null, 'availability' => $o['availability'] ?? 'http://schema.org/InStock', 'url' => $d['url'] ?? null]];
    }
    public static function breadcrumbs(array $items): array
    {
        return ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => array_values(array_map(function ($idx, $it) {
            return ['@type' => 'ListItem', 'position' => $idx + 1, 'name' => $it['title'], 'item' => $it['url']];
        }, array_keys($items), $items))];
    }
}
