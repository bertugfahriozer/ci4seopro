<?php
namespace ci4seopro\Libraries\Seo\Ai;

class AiTxt
{
    public function __construct(protected AiPolicy $policy){}
    public function body(string $baseUrl): string { return $this->policy->aiTxtBody($baseUrl); }
}
