<?php
namespace App\Libraries\Seo\Http;
trait CacheHeadersTrait{
    protected function withConditionalCache(\CodeIgniter\HTTP\ResponseInterface $res,string $body,?int $lastModifiedTs=null,int $maxAge=300){
        $etag='"'.sha1($body).'"'; $res->setHeader('ETag',$etag); if($lastModifiedTs)$res->setHeader('Last-Modified',gmdate('D, d M Y H:i:s',$lastModifiedTs).' GMT'); $res->setHeader('Cache-Control','public, max-age='.$maxAge);
        $req=service('request'); if($req->getHeaderLine('If-None-Match')===$etag) return $res->setStatusCode(304)->setBody('');
        if($lastModifiedTs){ $ims=strtotime($req->getHeaderLine('If-Modified-Since')?:''); if($ims and $ims >= $lastModifiedTs) return $res->setStatusCode(304)->setBody(''); }
        return $res->setBody($body);
    }
}
