<?php
namespace bertugfahriozer\ci4seopro\Controllers\Ai;

use CodeIgniter\Controller;

class AiApiController extends Controller
{
    public function context()
    {
        $url = $this->request->getGet('url');
        if (!$url) return $this->response->setJSON(['error'=>'url required'])->setStatusCode(400);

        $payload = [
          'url'       => site_url(ltrim($url,'/')),
          'title'     => 'Örnek Başlık',
          'lang'      => service('request')->getLocale(),
          'summary'   => 'Kısa özet.',
          'body_text' => 'Sayfa metninin sadeleştirilmiş halini burada döndürün.',
          'canonical' => site_url(ltrim($url,'/')),
          'lastmod'   => gmdate('c'),
          'jsonld'    => [],
        ];
        return $this->response->setJSON($payload);
    }
}
