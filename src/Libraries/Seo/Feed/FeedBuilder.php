<?php
namespace bertugfahriozer\ci4seopro\Libraries\Seo\Feed;

use Config\Seo;

class FeedBuilder
{
    public function __construct(protected Seo $cfg){}

    public function build(string $name): array
    {
        $def = $this->cfg->feeds[$name] ?? null;
        if (!$def || !$this->cfg->feedsEnabled) {
            return ['contentType'=>'text/plain', 'body'=>'Feed not found'];
        }
        $format = strtolower($def['format'] ?? 'rss2');
        $items  = $this->items($def);

        $siteBase = rtrim($this->cfg->baseUrl ?: site_url('/'), '/');
        $title = $def['title'] ?? ($this->cfg->siteName.' Feed');
        $link  = $siteBase . ($def['link'] ?? '/');
        $desc  = $def['desc'] ?? $this->cfg->siteName;

        switch ($format) {
            case 'rss2':
                return ['contentType'=>'application/rss+xml', 'body'=>$this->rss2($title,$link,$desc,$items,$siteBase)];
            case 'atom':
                return ['contentType'=>'application/atom+xml', 'body'=>$this->atom($title,$link,$desc,$items,$siteBase)];
            case 'jsonfeed':
                return ['contentType'=>'application/feed+json', 'body'=>$this->jsonfeed($title,$link,$desc,$items,$siteBase)];
            case 'json':
            default:
                return ['contentType'=>'application/json', 'body'=>json_encode(['title'=>$title,'home_page_url'=>$link,'items'=>$this->normalizeJson($items,$siteBase)], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)];
        }
    }

    protected function items(array $def): array
    {
        $items = [];
        if (($def['type'] ?? '') === 'static') {
            $items = $def['items'] ?? [];
        } elseif (($def['type'] ?? '') === 'callback' && !empty($def['callable']) && is_callable($def['callable'])) {
            $items = call_user_func($def['callable']);
        }
        return is_array($items) ? $items : [];
    }

    protected function rss2(string $title, string $link, string $desc, array $items, string $base): string
    {
        $xml = ['<?xml version="1.0" encoding="UTF-8"?>',
                '<rss version="2.0"><channel>',
                '<title>'.esc($title).'</title>',
                '<link>'.esc($link).'</link>',
                '<description>'.esc($desc).'</description>'];
        foreach ($items as $it) {
            $u = $base.($it['url'] ?? '/');
            $xml[] = '<item>';
            $xml[] = '<title>'.esc($it['title'] ?? '').'</title>';
            $xml[] = '<link>'.esc($u).'</link>';
            if (!empty($it['id'])) $xml[] = '<guid isPermaLink="false">'.esc($it['id']).'</guid>';
            if (!empty($it['summary'])) $xml[] = '<description>'.esc($it['summary']).'</description>';
            if (!empty($it['date'])) $xml[] = '<pubDate>'.gmdate('r', strtotime($it['date'])).'</pubDate>';
            $xml[] = '</item>';
        }
        $xml[]='</channel></rss>';
        return implode("\n",$xml);
    }

    protected function atom(string $title, string $link, string $desc, array $items, string $base): string
    {
        $feedId = $link.'/#atom';
        $xml = ['<?xml version="1.0" encoding="UTF-8"?>',
                '<feed xmlns="http://www.w3.org/2005/Atom">',
                '<title>'.esc($title).'</title>',
                '<id>'.esc($feedId).'</id>',
                '<link href="'.esc($link).'" />'];
        foreach ($items as $it) {
            $u = $base.($it['url'] ?? '/');
            $id = !empty($it['id']) ? $it['id'] : $u;
            $updated = !empty($it['date']) ? gmdate('c', strtotime($it['date'])) : gmdate('c');
            $xml[] = '<entry>';
            $xml[] = '<title>'.esc($it['title'] ?? '').'</title>';
            $xml[] = '<id>'.esc($id).'</id>';
            $xml[] = '<link href="'.esc($u).'" />';
            $xml[] = '<updated>'.$updated.'</updated>';
            if (!empty($it['summary'])) $xml[] = '<summary>'.esc($it['summary']).'</summary>';
            $xml[] = '</entry>';
        }
        $xml[]='</feed>';
        return implode("\n",$xml);
    }

    protected function jsonfeed(string $title, string $link, string $desc, array $items, string $base): string
    {
        $feed = [
            "version" => "https://jsonfeed.org/version/1.1",
            "title" => $title,
            "home_page_url" => $link,
            "description" => $desc,
            "items" => $this->normalizeJson($items, $base),
        ];
        return json_encode($feed, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    protected function normalizeJson(array $items, string $base): array
    {
        $out = [];
        foreach ($items as $it) {
            $u = $base . ($it['url'] ?? '/');
            $out[] = [
                'id' => $it['id'] ?? $u,
                'url'=> $u,
                'title' => $it['title'] ?? '',
                'summary' => $it['summary'] ?? null,
                'content_text' => $it['content'] ?? null,
                'date_published' => !empty($it['date']) ? gmdate('c', strtotime($it['date'])) : null,
            ];
        }
        return $out;
    }
}
