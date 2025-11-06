<?php
namespace bertugfahriozer\ci4seopro\Config;

use CodeIgniter\Config\BaseService;
use App\Libraries\Seo\Search\SeoManager;
use App\Libraries\Seo\Ai\AiPolicy;

class Services extends BaseService
{
    public static function seosearch(?Seo $config = null, bool $getShared = true): SeoManager
    {
        if ($getShared) return static::getSharedInstance('seosearch', $config);
        $config = $config ?? config('Seo');
        return new SeoManager($config);
    }

    public static function seopolicy(?Seo $config = null, bool $getShared = true): AiPolicy
    {
        if ($getShared) return static::getSharedInstance('seopolicy', $config);
        $config = $config ?? config('Seo');
        return new AiPolicy($config);
    }
}
