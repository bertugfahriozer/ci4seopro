<?php

namespace ci4seopro\Libraries\Seo\Search;

use ci4seopro\Config\Seo;

class PingService
{
    protected string $logFile;
    public function __construct(protected Seo $cfg)
    {
        $this->logFile = WRITEPATH . 'logs/seo-ping.log';
    }
    public function pingAll(): array
    {
        $base = rtrim($this->cfg->baseUrl ?: site_url('/'), '/');
        $s = $base . '/sitemap.xml';
        $r = [];
        $r['bing'] = !empty($this->cfg->pings['bing']) ? $this->retry(fn() => $this->get('https://www.bing.com/ping?sitemap=' . urlencode($s))) : 'SKIP';
        $r['yandex'] = !empty($this->cfg->pings['yandex']) ? $this->retry(fn() => $this->get('https://yandex.com/ping?sitemap=' . urlencode($s))) : 'SKIP';
        $r['pubsub'] = !empty($this->cfg->pings['pubsub']) ? $this->retry(fn() => $this->post('https://pubsubhubbub.appspot.com/publish', ['hub.mode' => 'publish', 'hub.url' => $s])) : 'SKIP';
        $this->log($r);
        return $r;
    }
    protected function retry(callable $fn, int $max = 3, int $sleepMs = 250): string
    {
        for ($i = 1; $i <= $max; $i++) {
            $res = $fn();
            if ($res === 'OK') return 'OK';
            usleep($sleepMs * 1000);
            $sleepMs *= 2;
        }
        return 'FAIL';
    }
    protected function get(string $url): string
    {
        $ctx = stream_context_create(['http' => ['method' => 'GET', 'timeout' => 5]]);
        $res = @file_get_contents($url, false, $ctx);
        return $res ? 'OK' : 'FAIL';
    }
    protected function post(string $url, array $data): string
    {
        $ctx = stream_context_create(['http' => ['method' => 'POST', 'header' => "Content-Type: application/x-www-form-urlencoded\r\n", 'content' => http_build_query($data), 'timeout' => 5]]);
        $res = @file_get_contents($url, false, $ctx);
        return $res ? 'OK' : 'FAIL';
    }
    protected function log(array $row): void
    {
        $line = '[' . gmdate('c') . '] ' . json_encode($row, JSON_UNESCAPED_UNICODE) . PHP_EOL;
        @file_put_contents($this->logFile, $line, FILE_APPEND);
    }
}
