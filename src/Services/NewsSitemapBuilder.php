<?php

namespace bertugfahriozer\ci4seopro\Services;

use bertugfahriozer\ci4seopro\Config\Seo;

class NewsSitemapBuilder
{
    public function __construct(protected Seo $cfg) {}

    public function build(array $items): string
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset/>');
        $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $xml->addAttribute('xmlns:news', 'http://www.google.com/schemas/sitemap-news/0.9');

        foreach ($items as $it) {
            $url = $xml->addChild('url');
            $url->addChild('loc', htmlspecialchars((string)$it['loc'], ENT_XML1));

            $news = $url->addChild('news:news', null, 'http://www.google.com/schemas/sitemap-news/0.9');
            $pub = $news->addChild('news:publication', null, 'http://www.google.com/schemas/sitemap-news/0.9');
            $pub->addChild('news:name', htmlspecialchars((string)($it['publication_name'] ?? $this->cfg->siteName), ENT_XML1), 'http://www.google.com/schemas/sitemap-news/0.9');
            $pub->addChild('news:language', htmlspecialchars((string)($it['publication_language'] ?? 'tr'), ENT_XML1), 'http://www.google.com/schemas/sitemap-news/0.9');

            $news->addChild('news:publication_date', htmlspecialchars((string)($it['publication_date'] ?? date('c')), ENT_XML1), 'http://www.google.com/schemas/sitemap-news/0.9');
            $news->addChild('news:title', htmlspecialchars((string)($it['title'] ?? ''), ENT_XML1), 'http://www.google.com/schemas/sitemap-news/0.9');

            if (!empty($it['keywords']) && is_array($it['keywords'])) {
                $news->addChild('news:keywords', htmlspecialchars(implode(', ', $it['keywords']), ENT_XML1), 'http://www.google.com/schemas/sitemap-news/0.9');
            }
        }

        return $xml->asXML();
    }
}
