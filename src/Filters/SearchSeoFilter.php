<?php

namespace ci4seopro\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SearchSeoFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null) {}

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        if (stripos($response->getHeaderLine('Content-Type'), 'text/html') === false) return;

        $seo = service('seosearch');
        $html = $response->getBody();
        $path = $request->getPath();

        $html = $seo->injectIntoHtml($html, [
            'route'  => $path,
            'locale' => service('request')->getLocale(),
        ]);

        $response->setBody($html);
    }
}
