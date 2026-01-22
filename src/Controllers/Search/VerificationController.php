<?php

namespace ci4seopro\Controllers\Search;

use ci4seopro\Config\Seo;
use CodeIgniter\Controller;

class VerificationController extends Controller
{
    public function html(string $filename)
    {
        $cfg = new Seo;
        $files = $cfg->verify['files'] ?? [];
        if (!isset($files[$filename])) return $this->response->setStatusCode(404);
        return $this->response->setContentType('text/html; charset=utf-8')->setBody($files[$filename]);
    }
    public function wellKnown(string $name)
    {
        $cfg = new Seo;
        $wk = $cfg->verify['wellKnown'] ?? [];
        if (!isset($wk[$name])) return $this->response->setStatusCode(404);
        return $this->response->setContentType('text/plain; charset=utf-8')->setBody($wk[$name]);
    }
}
