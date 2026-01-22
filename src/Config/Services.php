<?php
namespace ci4seopro\Config;

use CodeIgniter\Config\BaseService;
use ci4seopro\Libraries\Seo\Search\SeoManager;
use ci4seopro\Libraries\Seo\Ai\AiPolicy;
use ci4seopro\Config\Seo;

class Services extends BaseService
{
    public static function seosearch(?Seo $config = null, bool $getShared = true): SeoManager
    {
        if ($getShared) return static::getSharedInstance('seosearch', $config);
        $config = $config ?? new Seo;
        return new SeoManager($config);
    }

    public static function seopolicy(?Seo $config = null, bool $getShared = true): AiPolicy
    {
        if ($getShared) return static::getSharedInstance('seopolicy', $config);
        $config = $config ?? new Seo;
        return new AiPolicy($config);
    }
}
