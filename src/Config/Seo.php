<?php

namespace ci4seopro\Config;

use CodeIgniter\Config\BaseConfig;

class Seo extends BaseConfig
{
    /** --------- Arama Motoru Bölümü --------- */
    public string $siteName   = 'My Site';
    public string $baseUrl    = '';
    public array  $locales    = ['tr-TR'];
    public ?string $defaultLocale = 'tr-TR';

    public array $templates = [
        'default' => ['title' => '{title} | {site}', 'desc' => '{excerpt.160}'],
        'home'    => ['title' => '{site} | {tagline}', 'desc' => '{summary.160}'],
    ];

    public array $rules = [
        ['pattern' => '/admin*',  'robots' => 'noindex,nofollow'],
        ['pattern' => '/search*', 'robots' => 'noindex,follow'],
    ];

    /** --------- Çoklu Sitemap --------- */
    public bool $sitemapIndexEnabled = true;
    public int  $sitemapChunkSize    = 10000;
    public array $sitemaps = [
        /* 'pages' => [
            'type' => 'static',
            'items' => [
                ['loc' => '/', 'changefreq' => 'weekly', 'priority' => 1.0],
                ['loc' => '/about', 'changefreq' => 'monthly', 'priority' => 0.6],
                ['loc' => '/contact', 'changefreq' => 'monthly', 'priority' => 0.6],
            ],
        ], */
        'pages' => [
                'type' => 'callback',
                'callable' => [\App\Models\PagesModel::class, 'sitemapItems'],
                'hreflang' => true,
            ],
        'blog' => [
            'type' => 'callback',
            'callable' => [\App\Models\BlogModel::class, 'sitemapItems'],
            'hreflang' => true,
        ],
    ];

    /** --------- AI/LLM --------- */
    public bool $aiEnabled = true;
    public array $aiHeaderRules = [
        ['pattern' => '/admin*',  'xrobots' => 'noindex, nofollow, noai'],
        ['pattern' => '/media/*.pdf', 'xrobots' => 'noindex, noai'],
    ];

    public array $aiTxt = [
        'contact'    => 'mailto:info@example.com',
        'policy'     => 'summary-allowed; attribution-required',
        'license'    => 'CC BY-NC 4.0',
        'rate-limit' => '1rps; burst=10',
    ];

    public array $aiAgents = [
        'GPTBot'          => ['allow' => ['/blog*', '/docs*'], 'disallow' => ['/admin*', '/search*']],
        'PerplexityBot'   => ['allow' => ['/blog*', '/docs*'], 'disallow' => ['/admin*', '/search*']],
        'Google-Extended' => ['allow' => ['/blog*', '/docs*'], 'disallow' => ['/admin*', '/search*']],
        '*'               => ['allow' => ['/*'], 'disallow' => []],
    ];

    /** --------- FEEDS (RSS/Atom/JSONFeed + LLM Changefeed) --------- */
    public bool $feedsEnabled = true;
    public array $feeds = [
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
    ];

    public array $verify = [
        // host bazlı meta tag’ler
        // '*' tüm hostlar için geçerli. İstersen 'example.com' gibi host anahtarı kullan.
        'meta' => [
            '*' => [
                // Google
                'google-site-verification' => '',
                // Bing
                'msvalidate.01'            => '',
                // Yandex
                'yandex-verification'      => '',
                // Pinterest
                'p:domain_verify'          => '',
                // Facebook
                'facebook-domain-verification' => '',
                // Naver/Baidu gibi ekleyebilirsin:
                // 'naver-site-verification'  => 'NAVER_TOKEN',
                // 'baidu-site-verification'  => 'BAIDU_TOKEN',
            ],
            // 'example.com' => [ ... hosta özel tokenlar ... ]
        ],

        // HTML doğrulama dosyaları (filename => exact body)
        // ör: https://site.com/googleXXXX.html dosyası için:
        'files' => [
            // 'googleXXXX.html' => 'google-site-verification: googleXXXX.html',
        ],

        // well-known / düz metin dosyaları
        'wellKnown' => [
            // 'security.txt' => "Contact: mailto:security@site.com\nPolicy: https://site.com/security\n",
            // 'ads.txt'      => "example.com, pub-0000000000000000, DIRECT, f08c47fec0942fa0\n",
        ],
    ];

    public array $pings = [
        'bing' => true,
        'yandex' => true,
        'pubsub' => false
    ];
}
