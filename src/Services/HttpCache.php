<?php

namespace bertugfahriozer\ci4SeoPro\Services;

use CodeIgniter\HTTP\ResponseInterface;

class HttpCache
{
    public static function withConditionalCaching(ResponseInterface $response, string $content, int $ttlSeconds = 3600): ResponseInterface
    {
        $etag = '"' . sha1($content) . '"';
        $lastMod = gmdate('D, d M Y H:i:s', time()) . ' GMT';

        $response->setHeader('ETag', $etag);
        $response->setHeader('Last-Modified', $lastMod);
        $response->setHeader('Cache-Control', 'public, max-age=' . $ttlSeconds);

        $req = service('request');
        $ifNoneMatch = $req->getHeaderLine('If-None-Match');

        if ($ifNoneMatch && trim($ifNoneMatch) === $etag) {
            return $response->setStatusCode(304);
        }

        return $response->setBody($content);
    }
}
