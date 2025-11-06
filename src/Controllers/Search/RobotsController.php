<?php
namespace bertugfahriozer\ci4seopro\Controllers\Search;

use CodeIgniter\Controller;

class RobotsController extends Controller
{
    public function index()
    {
        $policy = service('seopolicy');
        $lines = [
            "User-agent: *",
            "Allow: /",
            "Sitemap: ".site_url('sitemap.xml'),
        ];
        foreach ($policy->robotsAiBlocks() as $agent=>$rows) {
            $lines[]=""; $lines[]="User-agent: {$agent}";
            foreach($rows['allow'] as $p)    $lines[]="Allow: {$p}";
            foreach($rows['disallow'] as $p) $lines[]="Disallow: {$p}";
        }
        return $this->response->setContentType('text/plain')->setBody(implode("\n",$lines)."\n");
    }
}
