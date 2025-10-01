<?php

namespace bertugfahriozer\ci4seopro\Services;

use bertugfahriozer\ci4seopro\Config\Seo;

class VideoSitemapBuilder
{
    public function __construct(protected Seo $cfg) {}

    public function build(array $items): string
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset/>');
        $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $xml->addAttribute('xmlns:video', 'http://www.google.com/schemas/sitemap-video/1.1');

        foreach ($items as $it) {
            $url = $xml->addChild('url');
            $url->addChild('loc', htmlspecialchars((string)$it['loc'], ENT_XML1));

            foreach (($it['videos'] ?? []) as $v) {
                $video = $url->addChild('video:video', null, 'http://www.google.com/schemas/sitemap-video/1.1');
                $video->addChild('video:thumbnail_loc', htmlspecialchars((string)($v['thumbnail_loc'] ?? ''), ENT_XML1), 'http://www.google.com/schemas/sitemap-video/1.1');
                $video->addChild('video:title', htmlspecialchars((string)($v['title'] ?? ''), ENT_XML1), 'http://www.google.com/schemas/sitemap-video/1.1');
                $video->addChild('video:description', htmlspecialchars((string)($v['description'] ?? ''), ENT_XML1), 'http://www.google.com/schemas/sitemap-video/1.1');

                if (!empty($v['content_loc'])) $video->addChild('video:content_loc', htmlspecialchars((string)$v['content_loc'], ENT_XML1), 'http://www.google.com/schemas/sitemap-video/1.1');
                if (!empty($v['player_loc'])) $video->addChild('video:player_loc', htmlspecialchars((string)$v['player_loc'], ENT_XML1), 'http://www.google.com/schemas/sitemap-video/1.1');

                foreach (['duration', 'view_count'] as $intKey) {
                    if (isset($v[$intKey])) $video->addChild('video:' . $intKey, (int)$v[$intKey], 'http://www.google.com/schemas/sitemap-video/1.1');
                }
                if (isset($v['rating'])) $video->addChild('video:rating', number_format((float)$v['rating'], 1), 'http://www.google.com/schemas/sitemap-video/1.1');
                if (!empty($v['publication_date'])) $video->addChild('video:publication_date', htmlspecialchars((string)$v['publication_date'], ENT_XML1), 'http://www.google.com/schemas/sitemap-video/1.1');
                if (!empty($v['family_friendly'])) $video->addChild('video:family_friendly', in_array(strtolower((string)$v['family_friendly']), ['yes', 'no']) ? $v['family_friendly'] : 'yes', 'http://www.google.com/schemas/sitemap-video/1.1');
                if (!empty($v['tag']) && is_array($v['tag'])) {
                    foreach ($v['tag'] as $tag) {
                        $video->addChild('video:tag', htmlspecialchars((string)$tag, ENT_XML1), 'http://www.google.com/schemas/sitemap-video/1.1');
                    }
                }
            }
        }

        return $xml->asXML();
    }
}
