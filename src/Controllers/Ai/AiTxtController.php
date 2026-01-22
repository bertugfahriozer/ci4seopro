<?php
namespace ci4seopro\Controllers\Ai;

use CodeIgniter\Controller;

class AiTxtController extends Controller
{
    public function index()
    {
        $body = service('seopolicy')->aiTxtBody(site_url('/'));
        return $this->response->setContentType('text/plain')->setBody($body);
    }
}
