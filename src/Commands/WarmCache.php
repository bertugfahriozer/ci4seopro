<?php

namespace bertugfahriozer\ci4SeoPro\Commands;

use Bertug\SeoPro\Config\Seo;
use Bertug\SeoPro\Services\SitemapBuilder;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class WarmCache extends BaseCommand
{
    protected $group = 'SEO';
    protected $name = 'seo:warm-cache';
    protected $description = 'Warm package caches (robots, llms, sitemaps, rss).';

    public function run(array $params)
    {
        $cfg = config(Seo::class);
        $cache = cache();
        $ttl = $cfg->appCacheTTL;

        // robots
        $lines = [];
        foreach (['*'] as $ua) {
            $lines[] = "User-agent: {$ua}";
            foreach ($cfg->robotsGlobalAllow as $p) $lines[] = "Allow: {$p}";
            foreach ($cfg->robotsGlobalDisallow as $p) $lines[] = "Disallow: {$p}";
            $lines[] = "";
        }
        foreach ($cfg->aiUserAgents as $ua) {
            $lines[] = "User-agent: {$ua}";
            $lines[] = ($cfg->aiPolicy === 'Disallow') ? "Disallow: /" : "Allow: /";
            $lines[] = "";
        }
        if ($cfg->enableSitemap) $lines[] = 'Sitemap: ' . rtrim($cfg->siteUrl,'/') . '/sitemap.xml';
        if ($cfg->enableNewsSitemap) $lines[] = 'Sitemap: ' . rtrim($cfg->siteUrl,'/') . '/sitemap-news.xml';
        if ($cfg->enableImageSitemap) $lines[] = 'Sitemap: ' . rtrim($cfg->siteUrl,'/') . '/sitemap-images.xml';
        if ($cfg->enableVideoSitemap) $lines[] = 'Sitemap: ' . rtrim($cfg->siteUrl,'/') . '/sitemap-videos.xml';
        $cache->save('seo_pro_robots', implode("\n", $lines) . "\n", $ttl);

        // llms
        $ll = [
            "# LLMs access policy",
            "Site: " . $cfg->siteUrl,
            "Policy: " . $cfg->aiPolicy,
            "Contact: " . $cfg->contactEmail,
            "",
            "# Known AI user-agents"
        ];
        foreach ($cfg->aiUserAgents as $ua) {
            $ll[] = "User-agent: {$ua}";
            $ll[] = "Policy: " . $cfg->aiPolicy;
            $ll[] = "";
        }
        $cache->save('seo_pro_llms', implode("\n", $ll) . "\n", $ttl);

        // standard sitemap index + first chunk
        if ($cfg->enableSitemap) {
            $builder = new SitemapBuilder($cfg);
            $all = $builder->collectAll();
            $chunk = max(1, (int)$cfg->sitemapChunkSize);
            $parts = max(1, (int)ceil(count($all) / $chunk));
            $cache->save('seo_pro_sitemap_index', $builder->buildIndex(['count' => $parts]), $ttl);
            $cache->save('seo_pro_sitemap_1', $builder->buildChunk(array_slice($all, 0, $chunk)), $ttl);
        }

        // placeholders for modular maps; they will be generated on first hit based on providers.
        $cache->save('seo_pro_sitemap_news', '', $ttl);
        $cache->save('seo_pro_sitemap_images', '', $ttl);
        $cache->save('seo_pro_sitemap_videos', '', $ttl);
        $cache->save('seo_pro_rss', '', $ttl);

        CLI::write('Warmed.');
    }
}
