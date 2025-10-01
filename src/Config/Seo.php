<?php

namespace bertugfahriozer\ci4SeoPro\Config;

use CodeIgniter\Config\BaseConfig;

class Seo extends BaseConfig
{
    public string $siteName = 'My Awesome Site';
    public string $siteDescription = 'High-quality content.';
    public string $siteUrl = 'https://example.com';
    public string $defaultLocale = 'tr';
    public array  $locales = ['tr','en'];
    public array  $localeDomains = [];
    public string $defaultImage = 'https://example.com/assets/og-default.jpg';
    public string $twitterSite  = '@example';

    public bool $httpCaching = true;
    public int  $httpCacheSeconds = 3600;
    public bool $useAppCache = true;
    public int  $appCacheTTL = 3600;

    public bool $serveRobots = true;
    public array $robotsGlobalAllow = ['/'];
    public array $robotsGlobalDisallow = ['/admin'];

    public bool $serveLlms = true;
    public string $llmsPath = '/llms.txt';
    public array $aiUserAgents = ['GPTBot','CCBot','ClaudeBot','Claude-Web','PerplexityBot','Amazonbot','Bytespider','Google-Extended','GoogleOther','Meta-ExternalAgent'];
    public string $aiPolicy = 'Allow';
    public string $contactEmail = 'webmaster@example.com';

    public bool $enableSitemap = true;
    public int $sitemapChunkSize = 5000;
    public array $sitemapProviders = [];

    public bool $enableRss = true;
    public string $rssTitle = 'Example RSS';
    public string $rssDescription = 'Latest updates';
    public string $rssLanguage = 'tr-TR';
    public $rssItemsProvider = null;

    public bool $enableNewsSitemap = true;
    public $newsProvider = null;

    public bool $enableImageSitemap = true;
    public $imageProvider = null;

    public bool $enableVideoSitemap = true;
    public $videoProvider = null;
}
