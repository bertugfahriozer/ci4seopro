<?php
namespace bertugfahriozer\ci4seopro\Controllers\Feed;

use CodeIgniter\Controller;
use App\Libraries\Seo\Feed\FeedBuilder;

class FeedController extends Controller
{
    public function show(string $name)
    {
        $cfg = config('Seo');
        $builder = new FeedBuilder($cfg);
        $res = $builder->build($name);
        return $this->response->setContentType($res['contentType'])->setBody($res['body']);
    }
}
