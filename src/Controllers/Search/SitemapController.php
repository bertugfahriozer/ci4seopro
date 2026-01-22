<?php
namespace ci4seopro\Controllers\Search;

use CodeIgniter\Controller;

class SitemapController extends Controller
{
    public function index()
    {
        $xml = service('seosearch')->sitemapIndex();
        return $this->response->setContentType('application/xml')->setBody($xml);
    }

    public function chunk(string $name)
    {
        $xml = service('seosearch')->sitemapXml($name);
        return $this->response->setContentType('application/xml')->setBody($xml);
    }
}
