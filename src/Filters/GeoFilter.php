<?php
declare(strict_types=1);

namespace ci4seopro\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class GeoFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null) {}

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        if (stripos($response->getHeaderLine('Content-Type'), 'text/html') === false) return;
        $geo = service('geo');
        if (!$geo->isEnabled()) return;
        $response->setBody($geo->injectIntoHtml($response->getBody()));
    }
}
