<?php
namespace bertugfahriozer\ci4seopro\Config;

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
        'pages' => [
            'type' => 'static',
            'items' => [
                ['loc'=>'/','changefreq'=>'weekly','priority'=>1.0],
                ['loc'=>'/about','changefreq'=>'monthly','priority'=>0.6],
            ],
        ],
        'blog' => [
            'type' => 'callback',
            'callable' => [\App\Models\BlogModel::class, 'sitemapItems'],
            'hreflang' => true,
        ],
    ];

    /** --------- AI/LLM --------- */
    public array $aiHeaderRules = [
        ['pattern'=>'/admin*',  'xrobots'=>'noindex, nofollow, noai'],
        ['pattern'=>'/media/*.pdf', 'xrobots'=>'noindex, noai'],
    ];

    public array $aiTxt = [
        'contact'    => 'mailto:info@example.com',
        'policy'     => 'summary-allowed; attribution-required',
        'license'    => 'CC BY-NC 4.0',
        'rate-limit' => '1rps; burst=10',
    ];

    public array $aiAgents = [
        'GPTBot'          => ['allow'=>['/blog*','/docs*'], 'disallow'=>['/admin*','/search*']],
        'PerplexityBot'   => ['allow'=>['/blog*','/docs*'], 'disallow'=>['/admin*','/search*']],
        'Google-Extended' => ['allow'=>['/blog*','/docs*'], 'disallow'=>['/admin*','/search*']],
        '*'               => ['allow'=>['/*'], 'disallow'=>[]],
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
}
