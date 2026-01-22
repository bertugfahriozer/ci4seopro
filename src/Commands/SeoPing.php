<?php

namespace ci4seopro\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use ci4seopro\Libraries\Seo\Search\PingService;
use ci4seopro\Config\Seo;

class SeoPing extends BaseCommand
{
    protected $group = 'SEO';
    protected $name = 'seo:ping';
    protected $description = 'Sitemap ping işlemi (Bing, Yandex, PubSub).';
    public function run(array $params)
    {
        $cfg = new Seo;
        $p = new PingService($cfg);
        $r = $p->pingAll();
        foreach ($r as $k => $v) {
            CLI::write("$k: $v", $v === 'OK' ? 'green' : 'red');
        }
    }
}
