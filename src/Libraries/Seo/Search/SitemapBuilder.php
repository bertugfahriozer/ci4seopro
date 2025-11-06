<?php

namespace bertugfahriozer\ci4seopro\Libraries\Seo\Search;

use Config\Seo;

class SitemapBuilder
{
    public function __construct(protected Seo $cfg) {}

    public function buildIndex(): string
    {
        if (!$this->cfg->sitemapIndexEnabled) return $this->emptyUrlset();
        $base = rtrim($this->cfg->baseUrl ?: site_url('/'), '/');
        $xml = [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
        ];
        foreach (array_keys($this->cfg->sitemaps) as $name) {
            $loc = $base . '/sitemap-' . $name . '.xml';
            $xml[] = '<sitemap><loc>' . esc($loc) . '</loc></sitemap>';
        }
        $xml[] = '</sitemapindex>';
        return implode("\n", $xml);
    }

    public function buildChunk(string $name): string
    {
        $def = $this->cfg->sitemaps[$name] ?? null;
        if (!$def) return $this->emptyUrlset();
        $base = rtrim($this->cfg->baseUrl ?: site_url('/'), '/');

        $items = [];
        if (($def['type'] ?? '') === 'static') {
            $items = $def['items'] ?? [];
        } elseif (($def['type'] ?? '') === 'callback' && !empty($def['callable']) && is_callable($def['callable'])) {
            $items = call_user_func($def['callable']);
        }

        $xml = [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">'
        ];

        foreach ($items as $it) {
            $loc = $base . ($it['loc'] ?? '/');
            $xml[] = '<url>';
            $xml[] = '<loc>' . esc($loc) . '</loc>';
            if (!empty($it['lastmod'])) $xml[] = '<lastmod>' . gmdate('c', strtotime($it['lastmod'])) . '</lastmod>';
            if (!empty($it['changefreq'])) $xml[] = '<changefreq>' . $it['changefreq'] . '</changefreq>';
            if (!empty($it['priority'])) $xml[] = '<priority>' . $it['priority'] . '</priority>';
            if (!empty($def['hreflang']) && !empty($it['alternates'])) {
                foreach ($it['alternates'] as $alt) {
                    $xml[] = '<xhtml:link rel="alternate" hreflang="' . esc($alt['lang']) . '" href="' . esc($base . $alt['loc']) . '"/>';
                }
            }
            $xml[] = '</url>';
        }

        $xml[] = '</urlset>';
        return implode("\n", $xml);
    }

    protected function emptyUrlset(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>';
    }
}
