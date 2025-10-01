<?php

namespace bertugfahriozer\ci4SeoPro\Services;

use bertugfahriozer\ci4SeoPro\Config\Seo;

class RssBuilder
{
    public function __construct(protected Seo $cfg) {}

    public function build(array $items): string
    {
        $rss = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rss/>');
        $rss->addAttribute('version', '2.0');
        $channel = $rss->addChild('channel');
        $channel->addChild('title', htmlspecialchars($this->cfg->rssTitle, ENT_XML1));
        $channel->addChild('link', htmlspecialchars($this->cfg->siteUrl, ENT_XML1));
        $channel->addChild('description', htmlspecialchars($this->cfg->rssDescription, ENT_XML1));
        $channel->addChild('language', $this->cfg->rssLanguage);
        $channel->addChild('lastBuildDate', gmdate(DATE_RSS));

        foreach ($items as $it) {
            $item = $channel->addChild('item');
            $item->addChild('title', htmlspecialchars((string)($it['title'] ?? ''), ENT_XML1));
            $item->addChild('link', htmlspecialchars((string)($it['link'] ?? ''), ENT_XML1));
            $item->addChild('guid', htmlspecialchars((string)($it['link'] ?? ''), ENT_XML1));
            $item->addChild('description', htmlspecialchars((string)($it['desc'] ?? ''), ENT_XML1));
            $date = $it['date'] ?? time();
            $timestamp = is_numeric($date) ? (int)$date : strtotime((string)$date);
            $item->addChild('pubDate', gmdate(DATE_RSS, $timestamp ?: time()));
            if (!empty($it['author'])) {
                $item->addChild('author', htmlspecialchars((string)$it['author'], ENT_XML1));
            }
        }

        return $rss->asXML();
    }
}
