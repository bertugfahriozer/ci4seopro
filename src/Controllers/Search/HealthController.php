<?php

namespace ci4seopro\Controllers\Seo;

use ci4seopro\Config\Seo;
use CodeIgniter\Controller;

class HealthController extends Controller
{
    public function index()
    {
        $cfg = new Seo;
        $res = ['baseUrl' => $cfg->baseUrl ?: site_url('/'), 'sitemaps' => array_keys($cfg->sitemaps ?? []), 'feeds' => array_keys($cfg->feeds ?? []), 'verify_meta_any' => !empty($cfg->verify['meta'] ?? []), 'verify_files_any' => !empty($cfg->verify['files'] ?? []), 'pings' => $cfg->pings ?? [], 'ai_agents' => array_keys($cfg->aiAgents ?? []), 'routes' => ['robots' => site_url('robots.txt'), 'sitemap_index' => site_url('sitemap.xml'), 'ai_txt' => site_url('.well-known/ai.txt')], 'ping_log_present' => is_file(WRITEPATH . 'logs/seo-ping.log')];
        return $this->response->setJSON($res);
    }
}
