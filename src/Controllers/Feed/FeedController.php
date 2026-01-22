<?php
namespace ci4seopro\Controllers\Feed;

use ci4seopro\Config\Seo;
use CodeIgniter\Controller;
use ci4seopro\Libraries\Seo\Feed\FeedBuilder;

class FeedController extends Controller
{
    public function show(string $name)
    {
        $cfg = new Seo;
        $builder = new FeedBuilder($cfg);
        $res = $builder->build($name);
        return $this->response->setContentType($res['contentType'])->setBody($res['body']);
    }
}
