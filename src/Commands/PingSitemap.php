<?php

namespace bertugfahriozer\ci4seopro\Commands;

use Bertug\SeoPro\Config\Seo;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class PingSitemap extends BaseCommand
{
    protected $group = 'SEO';
    protected $name = 'seo:ping-sitemap';
    protected $description = 'Ping search engines with sitemap URL.';

    public function run(array $params)
    {
        $cfg = config(Seo::class);
        $sitemap = rtrim($cfg->siteUrl, '/') . '/sitemap.xml';
        $targets = [
            'google' => 'https://www.google.com/ping?sitemap=' . rawurlencode($sitemap),
            'bing'   => 'https://www.bing.com/ping?sitemap=' . rawurlencode($sitemap),
        ];
        foreach ($targets as $name => $url) {
            $ok = $this->httpGet($url);
            CLI::write(($ok ? 'OK' : 'FAIL') . " - $name : $url");
        }
    }

    protected function httpGet(string $url): bool
    {
        try {
            $ctx = stream_context_create(['http' => ['method' => 'GET', 'timeout' => 6]]);
            return @file_get_contents($url, false, $ctx) !== false;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
