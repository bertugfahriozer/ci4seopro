<?php

namespace bertugfahriozer\ci4SeoPro\Controllers;

use Bertug\SeoPro\Config\Seo as SeoConfig;
use Bertug\SeoPro\Services\HttpCache;
use Bertug\SeoPro\Services\RssBuilder;
use Bertug\SeoPro\Services\SitemapBuilder;
use Bertug\SeoPro\Services\NewsSitemapBuilder;
use Bertug\SeoPro\Services\ImageSitemapBuilder;
use Bertug\SeoPro\Services\VideoSitemapBuilder;
use CodeIgniter\HTTP\ResponseInterface;

class SeoController extends \CodeIgniter\Controller
{
    protected SeoConfig $cfg;

    public function __construct()
    {
        $this->cfg = config(SeoConfig::class);
    }

    public function robots(): ResponseInterface
    {
        if (! $this->cfg->serveRobots) return $this->response->setStatusCode(404);

        $cacheKey = 'seo_pro_robots';
        $body = $this->cfg->useAppCache ? cache()->get($cacheKey) : null;

        if ($body === null) {
            $lines = [];
            foreach (['*'] as $ua) {
                $lines[] = "User-agent: {$ua}";
                foreach ($this->cfg->robotsGlobalAllow as $path) $lines[] = "Allow: {$path}";
                foreach ($this->cfg->robotsGlobalDisallow as $path) $lines[] = "Disallow: {$path}";
                $lines[] = "";
            }
            foreach ($this->cfg->aiUserAgents as $ua) {
                $lines[] = "User-agent: {$ua}";
                $lines[] = ($this->cfg->aiPolicy === 'Disallow') ? "Disallow: /" : "Allow: /";
                $lines[] = "";
            }
            if ($this->cfg->enableSitemap) $lines[] = 'Sitemap: ' . rtrim($this->cfg->siteUrl, '/') . '/sitemap.xml';
            if ($this->cfg->enableNewsSitemap) $lines[] = 'Sitemap: ' . rtrim($this->cfg->siteUrl, '/') . '/sitemap-news.xml';
            if ($this->cfg->enableImageSitemap) $lines[] = 'Sitemap: ' . rtrim($this->cfg->siteUrl, '/') . '/sitemap-images.xml';
            if ($this->cfg->enableVideoSitemap) $lines[] = 'Sitemap: ' . rtrim($this->cfg->siteUrl, '/') . '/sitemap-videos.xml';

            $body = implode("\n", $lines) . "\n";
            if ($this->cfg->useAppCache) cache()->save($cacheKey, $body, $this->cfg->appCacheTTL);
        }

        $this->response->setHeader('Content-Type', 'text/plain; charset=UTF-8');
        if ($this->cfg->httpCaching) return HttpCache::withConditionalCaching($this->response, $body, $this->cfg->httpCacheSeconds);
        return $this->response->setBody($body);
    }

    public function llms(): ResponseInterface
    {
        if (! $this->cfg->serveLlms) return $this->response->setStatusCode(404);

        $cacheKey = 'seo_pro_llms';
        $body = $this->cfg->useAppCache ? cache()->get($cacheKey) : null;

        if ($body === null) {
            $lines = [
                "# LLMs access policy",
                "Site: " . $this->cfg->siteUrl,
                "Policy: " . $this->cfg->aiPolicy,
                "Contact: " . $this->cfg->contactEmail,
                "",
                "# Known AI user-agents"
            ];
            foreach ($this->cfg->aiUserAgents as $ua) {
                $lines[] = "User-agent: {$ua}";
                $lines[] = "Policy: " . $this->cfg->aiPolicy;
                $lines[] = "";
            }
            $body = implode("\n", $lines) . "\n";
            if ($this->cfg->useAppCache) cache()->save($cacheKey, $body, $this->cfg->appCacheTTL);
        }

        $this->response->setHeader('Content-Type', 'text/plain; charset=UTF-8');
        if ($this->cfg->httpCaching) return HttpCache::withConditionalCaching($this->response, $body, $this->cfg->httpCacheSeconds);
        return $this->response->setBody($body);
    }

    public function sitemapIndex(): ResponseInterface
    {
        if (! $this->cfg->enableSitemap) return $this->response->setStatusCode(404);

        $cacheKey = 'seo_pro_sitemap_index';
        $xml = $this->cfg->useAppCache ? cache()->get($cacheKey) : null;

        if ($xml === null) {
            $builder = new SitemapBuilder($this->cfg);
            $all = $builder->collectAll();
            $chunk = max(1, (int)$this->cfg->sitemapChunkSize);
            $parts = max(1, (int)ceil(count($all) / $chunk));
            $xml = $builder->buildIndex(['count' => $parts]);
            if ($this->cfg->useAppCache) cache()->save($cacheKey, $xml, $this->cfg->appCacheTTL);
        }

        $this->response->setHeader('Content-Type', 'application/xml; charset=UTF-8');
        if ($this->cfg->httpCaching) return HttpCache::withConditionalCaching($this->response, $xml, $this->cfg->httpCacheSeconds);
        return $this->response->setBody($xml);
    }

    public function sitemap(int $part = 1): ResponseInterface
    {
        if (! $this->cfg->enableSitemap) return $this->response->setStatusCode(404);

        $part = max(1, (int)$part);
        $cacheKey = 'seo_pro_sitemap_' . $part;
        $xml = $this->cfg->useAppCache ? cache()->get($cacheKey) : null;

        if ($xml === null) {
            $builder = new SitemapBuilder($this->cfg);
            $all = $builder->collectAll();
            $chunk = max(1, (int)$this->cfg->sitemapChunkSize);
            $offset = ($part - 1) * $chunk;
            $slice = array_slice($all, $offset, $chunk);
            if (empty($slice)) return $this->response->setStatusCode(404);
            $xml = $builder->buildChunk($slice);
            if ($this->cfg->useAppCache) cache()->save($cacheKey, $xml, $this->cfg->appCacheTTL);
        }

        $this->response->setHeader('Content-Type', 'application/xml; charset=UTF-8');
        if ($this->cfg->httpCaching) return HttpCache::withConditionalCaching($this->response, $xml, $this->cfg->httpCacheSeconds);
        return $this->response->setBody($xml);
    }

    public function rss(): ResponseInterface
    {
        if (! $this->cfg->enableRss) return $this->response->setStatusCode(404);

        $cacheKey = 'seo_pro_rss';
        $xml = $this->cfg->useAppCache ? cache()->get($cacheKey) : null;

        if ($xml === null) {
            $items = is_callable($this->cfg->rssItemsProvider) ? call_user_func($this->cfg->rssItemsProvider) : [];
            if (empty($items)) {
                $items[] = [
                    'title' => 'Hello World',
                    'link'  => rtrim($this->cfg->siteUrl,'/') . '/hello-world',
                    'desc'  => 'First item',
                    'date'  => time(),
                    'author'=> $this->cfg->contactEmail,
                ];
            }
            $builder = new RssBuilder($this->cfg);
            $xml = $builder->build($items);
            if ($this->cfg->useAppCache) cache()->save($cacheKey, $xml, $this->cfg->appCacheTTL);
        }

        $this->response->setHeader('Content-Type', 'application/rss+xml; charset=UTF-8');
        if ($this->cfg->httpCaching) return HttpCache::withConditionalCaching($this->response, $xml, $this->cfg->httpCacheSeconds);
        return $this->response->setBody($xml);
    }

    public function sitemapNews(): ResponseInterface
    {
        if (! $this->cfg->enableNewsSitemap) return $this->response->setStatusCode(404);

        $cacheKey = 'seo_pro_sitemap_news';
        $xml = $this->cfg->useAppCache ? cache()->get($cacheKey) : null;

        if ($xml === null) {
            $items = is_callable($this->cfg->newsProvider) ? call_user_func($this->cfg->newsProvider) : [];
            $builder = new NewsSitemapBuilder($this->cfg);
            $xml = $builder->build($items ?? []);
            if ($this->cfg->useAppCache) cache()->save($cacheKey, $xml, $this->cfg->appCacheTTL);
        }

        $this->response->setHeader('Content-Type', 'application/xml; charset=UTF-8');
        if ($this->cfg->httpCaching) return HttpCache::withConditionalCaching($this->response, $xml, $this->cfg->httpCacheSeconds);
        return $this->response->setBody($xml);
    }

    public function sitemapImages(): ResponseInterface
    {
        if (! $this->cfg->enableImageSitemap) return $this->response->setStatusCode(404);

        $cacheKey = 'seo_pro_sitemap_images';
        $xml = $this->cfg->useAppCache ? cache()->get($cacheKey) : null;

        if ($xml === null) {
            $items = is_callable($this->cfg->imageProvider) ? call_user_func($this->cfg->imageProvider) : [];
            $builder = new ImageSitemapBuilder($this->cfg);
            $xml = $builder->build($items ?? []);
            if ($this->cfg->useAppCache) cache()->save($cacheKey, $xml, $this->cfg->appCacheTTL);
        }

        $this->response->setHeader('Content-Type', 'application/xml; charset=UTF-8');
        if ($this->cfg->httpCaching) return HttpCache::withConditionalCaching($this->response, $xml, $this->cfg->httpCacheSeconds);
        return $this->response->setBody($xml);
    }

    public function sitemapVideos(): ResponseInterface
    {
        if (! $this->cfg->enableVideoSitemap) return $this->response->setStatusCode(404);

        $cacheKey = 'seo_pro_sitemap_videos';
        $xml = $this->cfg->useAppCache ? cache()->get($cacheKey) : null;

        if ($xml === null) {
            $items = is_callable($this->cfg->videoProvider) ? call_user_func($this->cfg->videoProvider) : [];
            $builder = new VideoSitemapBuilder($this->cfg);
            $xml = $builder->build($items ?? []);
            if ($this->cfg->useAppCache) cache()->save($cacheKey, $xml, $this->cfg->appCacheTTL);
        }

        $this->response->setHeader('Content-Type', 'application/xml; charset=UTF-8');
        if ($this->cfg->httpCaching) return HttpCache::withConditionalCaching($this->response, $xml, $this->cfg->httpCacheSeconds);
        return $this->response->setBody($xml);
    }
}
