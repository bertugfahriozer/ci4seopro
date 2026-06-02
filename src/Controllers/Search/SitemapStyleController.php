<?php
declare(strict_types=1);

namespace ci4seopro\Controllers\Search;

use CodeIgniter\Controller;

class SitemapStyleController extends Controller
{
    protected string $assetPath = __DIR__ . '/../../Assets/';

    public function xsl(): void
    {
        $this->serveAsset('sitemap.xsl', 'text/xsl');
    }

    public function css(): void
    {
        $this->serveAsset('sitemap.css', 'text/css');
    }

    private function serveAsset(string $file, string $contentType): void
    {
        $path = realpath($this->assetPath . $file);

        if (!$path || !file_exists($path)) {
            $this->response->setStatusCode(404)->setBody('Not Found')->send();
            exit;
        }

        $this->response
            ->setContentType($contentType)
            ->setHeader('Cache-Control', 'public, max-age=86400')
            ->setBody(file_get_contents($path))
            ->send();
        exit;
    }
}
