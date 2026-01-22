<?php

namespace ci4seopro\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SeoProSeed extends Seeder
{
    public function run()
    {
        $sm=rtrim(site_url('/'), '/').'/sitemap.xml';
        $this->db->table('seo_configurations')->insertBatch([
            [
                'config_key'   => 'siteName',
                'config_value' => 'My Site',
                'config_type'  => 'string',
                'description'  => 'Site adı',
            ],
            [
                'config_key'   => 'baseUrl',
                'config_value' => '',
                'config_type'  => 'string',
                'description'  => 'Site temel URL',
            ],
            [
                'config_key'   => 'locales',
                'config_value' => json_encode(['tr-TR']),
                'config_type'  => 'array',
                'description'  => 'Site dilleri',
            ],
            [
                'config_key'   => 'defaultLocale',
                'config_value' => 'tr-TR',
                'config_type'  => 'string',
                'description'  => 'Varsayılan dil',
            ],
            [
                'config_key'   => 'templates',
                'config_value' => json_encode([
                    'default' => ['title' => '{title} | {site}', 'desc' => '{excerpt.160}'],
                    'home'    => ['title' => '{site} | {tagline}', 'desc' => '{summary.160}'],
                ]),
                'config_type'  => 'object',
                'description'  => 'SEO şablonları',
            ],
            [
                'config_key'   => 'rules',
                'config_value' => json_encode([
                    ['pattern' => '/admin*',  'robots' => 'noindex,nofollow'],
                    ['pattern' => '/search*', 'robots' => 'noindex,follow'],
                ]),
                'config_type'  => 'array',
                'description'  => 'SEO kuralları',
            ],
            [
                'config_key'   => 'sitemapIndexEnabled',
                'config_value' => '1',
                'config_type'  => 'boolean',
                'description'  => 'Çoklu sitemap etkinleştirme',
            ],
            [
                'config_key'   => 'sitemapChunkSize',
                'config_value' => '10000',
                'config_type'  => 'integer',
                'description'  => 'Sitemap parçalama boyutu',
            ],
            [
                'config_key'   => 'sitemaps',
                'config_value' => json_encode([
                    'pages' => [
                        'type' => 'static',
                        'items' => [
                            ['loc' => '/', 'changefreq' => 'weekly', 'priority' => 1.0],
                            ['loc' => '/about', 'changefreq' => 'monthly', 'priority' => 0.6],
                        ],
                    ],
                    'blog' => [
                        'type' => 'callback',
                        'callable' => [\App\Models\BlogModel::class, 'sitemapItems'],
                        'hreflang' => true,
                    ],
                ]),
                'config_type'  => 'object',
                'description'  => 'Sitemap yapılandırmaları',
            ],
            [
                'config_key'   => 'aiEnabled',
                'config_value' => '1',
                'config_type'  => 'boolean',
                'description'  => 'Yapay zeka indexlemesi aktif mi?',
            ],
            [
                'config_key'   => 'aiHeaderRules',
                'config_value' => json_encode([
                    ['pattern' => '/admin*',  'xrobots' => 'noindex, nofollow, noai'],
                    ['pattern' => '/media/*.pdf', 'xrobots' => 'noindex, noai'],
                ]),
                'config_type'  => 'array',
                'description'  => 'AI başlık kuralları',
            ],
            [
                'config_key'   => 'aiTxt',
                'config_value' => json_encode([
                    'contact'    => 'mailto:info@example.com',
                    'policy'     => 'summary-allowed; attribution-required',
                    'license'    => 'CC BY-NC 4.0',
                    'rate-limit' => '1rps; burst=10',
                ]),
                'config_type'  => 'object',
                'description'  => 'AI metin yapılandırmaları',
            ],
            [
                'config_key'   => 'aiAgents',
                'config_value' => json_encode([
                    'GPTBot'          => ['allow' => ['/blog*', '/docs*'], 'disallow' => ['/admin*', '/search*']],
                    'PerplexityBot'   => ['allow' => ['/blog*', '/docs*'], 'disallow' => ['/admin*', '/search*']],
                    'Google-Extended' => ['allow' => ['/blog*', '/docs*'], 'disallow' => ['/admin*', '/search*']],
                    '*'               => ['allow' => ['/*'], 'disallow' => []],
                ]),
                'config_type'  => 'object',
                'description'  => 'AI ajan yapılandırmaları',
            ],
            [
                'config_key'   => 'feedsEnabled',
                'config_value' => '1',
                'config_type'  => 'boolean',
                'description'  => 'Beslemeler etkinleştirme',
            ],
            [
                'config_key'   => 'feeds',
                'config_value' => json_encode([
                    'blog-rss' => [
                        'format'   => 'rss2',
                        'type'     => 'callback',
                        'callable' => [\App\Models\BlogModel::class, 'feedItems'],
                        'title'    => 'My Site Blog (RSS)',
                        'link'     => '/blog',
                        'desc'     => 'Blog yazıları',
                    ],
                    'blog-atom' => [
                        'format'   => 'atom',
                        'type'     => 'callback',
                        'callable' => [\App\Models\BlogModel::class, 'feedItems'],
                        'title'    => 'My Site Blog (Atom)',
                        'link'     => '/blog',
                        'desc'     => 'Blog yazıları',
                    ],
                    'blog-json' => [
                        'format'   => 'jsonfeed',
                        'type'     => 'callback',
                        'callable' => [\App\Models\BlogModel::class, 'feedItems'],
                        'title'    => 'My Site Blog (JSON Feed)',
                        'link'     => '/blog',
                        'desc'     => 'Blog yazıları',
                    ],
                    'llm-changefeed' => [
                        'format'   => 'json',
                        'type'     => 'callback',
                        'callable' => [\App\Models\ChangefeedModel::class, 'items'],
                        'title'    => 'Content Changefeed',
                        'link'     => '/',
                        'desc'     => 'Recently changed resources for LLMs',
                    ],
                ]),
                'config_type'  => 'object',
                'description'  => 'Beslemeler yapılandırmaları',
            ],
            [
                'config_key'   => 'verify',
                'config_value' => json_encode([
                    'meta' => [
                        '*' => [
                            'google-site-verification' => 'GOOGLE_TOKEN',
                            'msvalidate.01'            => 'BING_TOKEN',
                            'yandex-verification'      => 'YANDEX_TOKEN',
                            'p:domain_verify'          => 'PINTEREST_TOKEN',
                            'facebook-domain-verification' => 'FB_TOKEN',
                        ],
                    ],
                    'files' => [],
                    'wellKnown' => [],
                ]),
                'config_type'  => 'object',
                'description'  => 'Doğrulama yapılandırmaları',
            ],
            [
                'config_key'   => 'pings',
                'config_value' => json_encode([
                    'bing' => true,
                    'yandex' => true,
                    'pubsub' => false
                ]),
                'config_type'  => 'object',
                'description'  => 'Ping yapılandırmaları',
            ],
            [
                'config_key'   => 'pingSE',
                'config_value' => json_encode([
                    'bing' =>
                    ['url'=> 'https://www.bing.com/ping?sitemap='.$sm,'opt'=>[]],
                    'yandex' =>
                    ['url'=> 'https://yandex.com/ping?sitemap='.$sm,'opt'=>[]],
                    'pubsub' =>
                    ['url'=> 'https://pubsubhubbub.appspot.com/publish','opt'=>[
                        'hub.mode' => 'publish', 'hub.url' =>$sm
                    ]]
                ]),
                'config_type'  => 'array',
                'description'  => 'Arama motorları ping url adresleri',
            ],
        ]);
    }
}
