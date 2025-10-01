<?php

namespace bertugfahriozer\ci4seopro\Services;

use bertugfahriozer\ci4seopro\Config\Seo;

class ImageSitemapBuilder
{
    public function __construct(protected Seo $cfg) {}

    public function build(array $items): string
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset/>');
        $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $xml->addAttribute('xmlns:image', 'http://www.google.com/schemas/sitemap-image/1.1');

        foreach ($items as $it) {
            $url = $xml->addChild('url');
            $url->addChild('loc', htmlspecialchars((string)$it['loc'], ENT_XML1));

            foreach (($it['images'] ?? []) as $img) {
                $image = $url->addChild('image:image', null, 'http://www.google.com/schemas/sitemap-image/1.1');
                $image->addChild('image:loc', htmlspecialchars((string)($img['loc'] ?? ''), ENT_XML1), 'http://www.google.com/schemas/sitemap-image/1.1');
                foreach (['caption', 'title', 'geo_location', 'license'] as $k) {
                    if (!empty($img[$k])) {
                        $image->addChild('image:' . $k, htmlspecialchars((string)$img[$k], ENT_XML1), 'http://www.google.com/schemas/sitemap-image/1.1');
                    }
                }
            }
        }

        return $xml->asXML();
    }
}
