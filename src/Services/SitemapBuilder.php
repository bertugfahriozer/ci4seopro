<?php

namespace bertugfahriozer\ci4SeoPro\Services;

use bertugfahriozer\ci4SeoPro\Config\Seo;

class SitemapBuilder
{
    public function __construct(protected Seo $cfg) {}

    public function collectAll(): array
    {
        $urls = [rtrim($this->cfg->siteUrl, '/') . '/'];
        foreach ($this->cfg->sitemapProviders as $entry) {
            $callable = $entry[0] ?? null;
            if (! $callable) continue;
            $list = is_callable($callable) ? $callable() : [];
            foreach ($list as $u) if (is_string($u)) $urls[] = $u;
        }
        return array_values(array_unique($urls));
    }

    public function buildIndex(array $partsCount, ?string $lastmod = null): string
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><sitemapindex/>');
        $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        $base = rtrim($this->cfg->siteUrl, '/');
        $lm = $lastmod ?: date('c');
        for ($i = 1; $i <= $partsCount['count']; $i++) {
            $sm = $xml->addChild('sitemap');
            $sm->addChild('loc', htmlspecialchars($base . '/sitemaps/' . $i . '.xml', ENT_XML1));
            $sm->addChild('lastmod', $lm);
        }
        return $xml->asXML();
    }

    public function buildChunk(array $urls, float $priority = 0.8, string $changefreq = 'weekly'): string
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset/>');
        $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        foreach ($urls as $loc) {
            $u = $xml->addChild('url');
            $u->addChild('loc', htmlspecialchars($loc, ENT_XML1));
            $u->addChild('lastmod', date('c'));
            $u->addChild('changefreq', $changefreq);
            $u->addChild('priority', number_format($priority, 1));
        }
        return $xml->asXML();
    }
}
