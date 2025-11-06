<?php
namespace bertugfahriozer\ci4seopro\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AiHeaderFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null) {}

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $policy = service('seopolicy')->xRobotsFor($request->getPath());
        if ($policy) $response->setHeader('X-Robots-Tag', $policy);
    }
}
