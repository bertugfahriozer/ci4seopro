<?php

use bertugfahriozer\ci4seopro\Config\Seo;

if (! function_exists('seo_meta')) {
    function seo_meta(array $overrides = []): string
    {
        $cfg = config(Seo::class);
        $title     = $overrides['title'] ?? $cfg->siteName;
        $desc      = $overrides['description'] ?? $cfg->siteDescription;
        $image     = $overrides['image'] ?? $cfg->defaultImage;
        $canonical = $overrides['canonical'] ?? current_url(true)->__toString();
        $locale    = $overrides['locale'] ?? $cfg->defaultLocale;
        $type      = $overrides['type'] ?? 'website';

        $tags = [
            '<title>' . esc($title) . '</title>',
            '<meta name="description" content="' . esc($desc) . '"/>',
            '<link rel="canonical" href="' . esc($canonical) . '"/>',
            '<meta property="og:title" content="' . esc($title) . '"/>',
            '<meta property="og:description" content="' . esc($desc) . '"/>',
            '<meta property="og:type" content="' . esc($type) . '"/>',
            '<meta property="og:url" content="' . esc($canonical) . '"/>',
            '<meta property="og:image" content="' . esc($image) . '"/>',
            '<meta property="og:locale" content="' . esc($locale) . '"/>',
            '<meta name="twitter:card" content="summary_large_image"/>',
            '<meta name="twitter:title" content="' . esc($title) . '"/>',
            '<meta name="twitter:description" content="' . esc($desc) . '"/>',
            '<meta name="twitter:image" content="' . esc($image) . '"/>',
        ];

        if (!empty($cfg->twitterSite)) {
            $tags[] = '<meta name="twitter:site" content="' . esc($cfg->twitterSite) . '"/>';
        }
        if (!empty($cfg->locales)) {
            foreach ($cfg->locales as $loc) {
                $base = $cfg->localeDomains[$loc] ?? $cfg->siteUrl;
                $tags[] = '<link rel="alternate" hreflang="' . esc($loc) . '" href="' . esc($base) . '"/>';
            }
        }
        if ($cfg->enableRss) {
            $tags[] = '<link rel="alternate" type="application/rss+xml" title="' . esc($cfg->rssTitle) . '" href="' . rtrim($cfg->siteUrl, '/') . '/rss.xml" />';
        }

        return implode("\n", $tags) . "\n";
    }
}
