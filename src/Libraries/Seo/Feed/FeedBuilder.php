<?php

namespace ci4seopro\Libraries\Seo\Feed;

use ci4seopro\Config\Seo;

class FeedBuilder
{
    public function __construct(protected Seo $cfg) {}

    public function build(string $name): array
    {
        $def = $this->cfg->feeds[$name] ?? null;
        if (!$def || !$this->cfg->feedsEnabled) return ['contentType' => 'text/plain', 'body' => 'Feed not found'];
        $format = strtolower($def['format'] ?? 'rss2');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = min(200, max(1, (int)($_GET['limit'] ?? 50)));
        $items = $this->items($def, $page, $limit);
        $base = rtrim($this->cfg->baseUrl ?: site_url('/'), '/');
        $title = $def['title'] ?? ($this->cfg->siteName . ' Feed');
        $link = $base . ($def['link'] ?? '/');
        $desc = $def['desc'] ?? $this->cfg->siteName;
        switch ($format) {
            case 'rss2':
                return ['contentType' => 'application/rss+xml', 'body' => $this->rss2($title, $link, $desc, $items, $base)];
            case 'atom':
                return ['contentType' => 'application/atom+xml', 'body' => $this->atom($title, $link, $desc, $items, $base)];
            case 'jsonfeed':
                return ['contentType' => 'application/feed+json', 'body' => $this->jsonfeed($title, $link, $desc, $items, $base, $page, $limit)];
            case 'json':
            default:
                return ['contentType' => 'application/json', 'body' => json_encode(['title' => $title, 'home_page_url' => $link, 'items' => $this->normalizeJson($items, $siteBase)], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)];
        }
    }

    protected function items(array $def,int $page,int $limit): array{
        if(($def['type']??'')==='static') return array_slice($def['items']??[],($page-1)*$limit,$limit);
        if(($def['type']??'')==='callback' && !empty($def['callable']) && is_callable($def['callable'])){ $ref=new \ReflectionFunction(\Closure::fromCallable($def['callable'])); $argc=$ref->getNumberOfParameters(); if($argc>=2) return call_user_func($def['callable'],$page,$limit); return call_user_func($def['callable']); }
        return [];
    }
    protected function rss2(string $title,string $link,string $desc,array $items,string $base): string{
        $x=['<?xml version="1.0" encoding="UTF-8"?>','<rss version="2.0"><channel>','<title>'.esc($title).'</title>','<link>'.esc($link).'</link>','<description>'.esc($desc).'</description>'];
        foreach($items as $it){ $u=$base.($it['url']??'/'); $x[]='<item>'; $x[]='<title>'.esc($it['title']??'').'</title>'; $x[]='<link>'.esc($u).'</link>'; if(!empty($it['id']))$x[]='<guid isPermaLink="false">'.esc($it['id']).'</guid>'; if(!empty($it['summary']))$x[]='<description>'.esc($it['summary']).'</description>'; if(!empty($it['date']))$x[]='<pubDate>'.gmdate('r',strtotime($it['date'])).'</pubDate>'; if(!empty($it['author']))$x[]='<author>'.esc($it['author']).'</author>'; if(!empty($it['categories']))foreach((array)$it['categories'] as $c)$x[]='<category>'.esc($c).'</category>'; if(!empty($it['enclosure'])){ $e=$it['enclosure']; $x[]='<enclosure url="'.esc($base.$e['url']).'" type="'.esc($e['type']??'application/octet-stream').'" length="'.esc((string)($e['length']??0)).'"/>'; } $x[]='</item>'; }
        $x[]='</channel></rss>'; return implode("\n",$x);
    }
    protected function atom(string $title,string $link,string $desc,array $items,string $base): string{
        $fid=$link.'/#atom'; $x=['<?xml version="1.0" encoding="UTF-8"?>','<feed xmlns="http://www.w3.org/2005/Atom">','<title>'.esc($title).'</title>','<id>'.esc($fid).'</id>','<link href="'.esc($link).'" />'];
        foreach($items as $it){ $u=$base.($it['url']??'/'); $id=!empty($it['id'])?$it['id']:$u; $upd=!empty($it['date'])?gmdate('c',strtotime($it['date'])):gmdate('c'); $x[]='<entry>'; $x[]='<title>'.esc($it['title']??'').'</title>'; $x[]='<id>'.esc($id).'</id>'; $x[]='<link href="'.esc($u).'" />'; $x[]='<updated>'.$upd.'</updated>'; if(!empty($it['summary']))$x[]='<summary>'.esc($it['summary']).'</summary>'; if(!empty($it['author']))$x[]='<author><name>'.esc($it['author']).'</name></author>'; if(!empty($it['image']))$x[]='<link rel="enclosure" href="'.esc($base.$it['image']).'" type="image/jpeg" />'; $x[]='</entry>'; }
        $x[]='</feed>'; return implode("\n",$x);

    protected function jsonfeed(string $title,string $link,string $desc,array $items,string $base,int $page,int $limit): string
    {
        $feed = [
            "version" => "https://jsonfeed.org/version/1.1",
            "title" => $title,
            "home_page_url" => $link,
            "description" => $desc,
            "page"=>$page,
            "limit"=>$limit,
            "items" => $this->normalizeJson($items, $base),
        ];
        return json_encode($feed, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    protected function normalizeJson(array $items,string $base): array{
        $out=[]; foreach($items as $it){ $u=$base.($it['url']??'/'); $row=['id'=>$it['id']??$u,'url'=>$u,'title'=>$it['title']??'','summary'=>$it['summary']??null,'content_text'=>$it['content']??null,'date_published'=>!empty($it['date'])?gmdate('c',strtotime($it['date'])):null,'authors'=>!empty($it['author'])?[['name'=>$it['author']]]:null,'tags'=>$it['categories']??null]; if(!empty($it['image']))$row['image']=$base.$it['image']; if(!empty($it['banner']))$row['banner_image']=$base.$it['banner']; if(!empty($it['enclosure'])){ $e=$it['enclosure']; $row['attachments']=[[ 'url'=>$base.$e['url'],'mime_type'=>$e['type']??'application/octet-stream','size_in_bytes'=>$e['length']??null ]]; } $out[]=$row; } return $out;
    }
}
