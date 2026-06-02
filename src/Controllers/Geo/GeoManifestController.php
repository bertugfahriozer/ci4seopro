<?php
declare(strict_types=1);

namespace ci4seopro\Controllers\Geo;

use CodeIgniter\Controller;
use ci4seopro\Config\Seo;
use ci4seopro\Libraries\Seo\Geo\GeoLlmBuilder;

class GeoManifestController extends Controller
{
    public function plugin()
    {
        $cfg = new Seo();

        if (!$cfg->geoAiManifest) {
            return $this->response->setStatusCode(404)->setBody('Not Found');
        }

        $builder  = new GeoLlmBuilder($cfg);
        $base     = rtrim($cfg->baseUrl ?: site_url('/'), '/');
        $manifest = $builder->aiPluginManifest($base);

        return $this->response
            ->setContentType('application/json')
            ->setBody(json_encode($manifest, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
