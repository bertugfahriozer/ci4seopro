<?php
declare(strict_types=1);

namespace ci4seopro\Controllers\Search;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

class SitemapStyleController extends Controller
{
    public function xsl(): ResponseInterface
    {
        return $this->serveAsset('sitemap.xsl', 'text/xsl');
    }

    public function css(): ResponseInterface
    {
        return $this->serveAsset('sitemap.css', 'text/css');
    }

    private function serveAsset(string $file, string $contentType): ResponseInterface
    {
        $path = realpath(__DIR__ . '/../../Assets/' . $file);

        if ($path === false || !is_file($path)) {
            return $this->response->setStatusCode(404)->setBody('Not Found');
        }

        return $this->response
            ->setContentType($contentType)
            ->setHeader('Cache-Control', 'public, max-age=86400')
            ->setBody(file_get_contents($path));
    }
}
