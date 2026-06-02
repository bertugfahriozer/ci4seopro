<?php
declare(strict_types=1);

namespace ci4seopro\Controllers\Geo;

use CodeIgniter\Controller;
use ci4seopro\Config\Seo;
use ci4seopro\Libraries\Seo\Geo\GeoLlmBuilder;

class GeoLlmController extends Controller
{
    public function llmsTxt()
    {
        $cfg     = new Seo();
        $builder = new GeoLlmBuilder($cfg);
        $base    = rtrim($cfg->baseUrl ?: site_url('/'), '/');

        return $this->response
            ->setContentType('text/plain; charset=UTF-8')
            ->setBody($builder->llmsTxtBody($base));
    }

    public function chunk()
    {
        $cfg = new Seo();

        if (!$cfg->geoEnabled || !$cfg->geoChunkEndpoint) {
            return $this->response->setStatusCode(404)->setBody('Not Found');
        }

        $url = $this->request->getGet('url');
        if (!$url || !str_starts_with($url, '/')) {
            return $this->response
                ->setStatusCode(400)
                ->setContentType('application/json')
                ->setBody(json_encode(['error' => 'url parameter required (must start with /)']));
        }

        $base = rtrim($cfg->baseUrl ?: site_url('/'), '/');
        $html = @file_get_contents($base . $url);

        if ($html === false) {
            return $this->response
                ->setStatusCode(404)
                ->setContentType('application/json')
                ->setBody(json_encode(['error' => 'page not found']));
        }

        $builder = new GeoLlmBuilder($cfg);
        $chunks  = $builder->chunkHtml($html);

        return $this->response
            ->setContentType('application/json')
            ->setBody(json_encode($chunks, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
