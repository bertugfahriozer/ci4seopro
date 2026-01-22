<?php

namespace ci4seopro\Libraries\Seo\Search;

use ci4seopro\Config\Seo;

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
        $items = $this->collectItems($def);
        $ns = ['xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"', 'xmlns:xhtml="http://www.w3.org/1999/xhtml"'];
        if (!empty($def['image'])) $ns[] = 'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"';
        if (!empty($def['video'])) $ns[] = 'xmlns:video="http://www.google.com/schemas/sitemap-video/1.1"';
        if (!empty($def['news'])) $ns[] = 'xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"';
        $xml = ['<?xml version="1.0" encoding="UTF-8"?>', '<urlset ' . implode(' ', $ns) . '>'];
        foreach ($items as $it) {
            $loc = $base . ($it['loc'] ?? '/');
            $xml[] = '<url>';
            $xml[] = '<loc>' . esc($loc) . '</loc>';
            if (!empty($it['lastmod'])) $xml[] = '<lastmod>' . gmdate('c', strtotime($it['lastmod'])) . '</lastmod>';
            if (!empty($it['changefreq'])) $xml[] = '<changefreq>' . $it['changefreq'] . '</changefreq>';
            if (!empty($it['priority'])) $xml[] = '<priority>' . $it['priority'] . '</priority>';
            if (!empty($def['hreflang']) && !empty($it['alternates'])) foreach ($it['alternates'] as $alt) {
                $xml[] = '<xhtml:link rel="alternate" hreflang="' . esc($alt['lang']) . '" href="' . esc($base . $alt['loc']) . '"/>';
            }
            if (!empty($def['image']) && !empty($it['images'])) foreach ($it['images'] as $img) {
                $xml[] = '<image:image>';
                $xml[] = '<image:loc>' . esc($base . $img['loc']) . '</image:loc>';
                if (!empty($img['caption'])) $xml[] = '<image:caption>' . esc($img['caption']) . '</image:caption>';
                if (!empty($img['title'])) $xml[] = '<image:title>' . esc($img['title']) . '</image:title>';
                $xml[] = '</image:image>';
            }
            if (!empty($def['video']) && !empty($it['videos'])) foreach ($it['videos'] as $v) {
                $xml[] = '<video:video>';
                if (!empty($v['content'])) $xml[] = '<video:content_loc>' . esc($base . $v['content']) . '</video:content_loc>';
                if (!empty($v['thumbnail'])) $xml[] = '<video:thumbnail_loc>' . esc($base . $v['thumbnail']) . '</video:thumbnail_loc>';
                if (!empty($v['title'])) $xml[] = '<video:title>' . esc($v['title']) . '</video:title>';
                if (!empty($v['desc'])) $xml[] = '<video:description>' . esc($v['desc']) . '</video:description>';
                $xml[] = '</video:video>';
            }
            if (!empty($def['news']) && !empty($it['news'])) {
                $n = $it['news'];
                $xml[] = '<news:news>';
                $xml[] = '<news:publication><news:name>' . esc($n['publication'] ?? $this->cfg->siteName) . '</news:name><news:language>' . esc($n['lang'] ?? 'tr') . '</news:language></news:publication>';
                if (!empty($n['title'])) $xml[] = '<news:title>' . esc($n['title']) . '</news:title>';
                if (!empty($n['pubDate'])) $xml[] = '<news:publication_date>' . gmdate('c', strtotime($n['pubDate'])) . '</news:publication_date>';
                if (!empty($n['genres'])) $xml[] = '<news:genres>' . esc(implode(',', (array)$n['genres'])) . '</news:genres>';
                $xml[] = '</news:news>';
            }
            $xml[] = '</url>';
        }
        $xml[] = '</urlset>';
        return implode("\n", $xml);
    }
    protected function collectItems(array $def): array
    {
        if (($def['type'] ?? '') === 'static') return $def['items'] ?? [];
        if (($def['type'] ?? '') === 'callback' && !empty($def['callable']) && is_callable($def['callable'])) {
            $items = call_user_func($def['callable']);
            return is_array($items) ? $items : [];
        }
        return [];
    }
    protected function emptyUrlset(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>' + "\n" + '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>';
    }
}
