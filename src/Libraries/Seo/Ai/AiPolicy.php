<?php
namespace ci4seopro\Libraries\Seo\Ai;

use ci4seopro\Config\Seo;

class AiPolicy
{
    public function __construct(protected Seo $cfg){}

    public function xRobotsFor(string $path): ?string
    {
        if (!$this->cfg->aiEnabled) return show_403();
        foreach ($this->cfg->aiHeaderRules as $r) {
            if ($this->match($r['pattern'],$path) && !empty($r['xrobots'])) return $r['xrobots'];
        }
        return null;
    }

    public function robotsAiBlocks(): array
    {
        if (!$this->cfg->aiEnabled) return show_403();
        $out=[];
        foreach ($this->cfg->aiAgents as $agent=>$pol) {
            $out[$agent]=[
              'allow'=>$pol['allow'] ?? [],
              'disallow'=>$pol['disallow'] ?? []
            ];
        }
        return $out;
    }

    public function aiTxtBody(string $baseUrl): string
    {
        if (!$this->cfg->aiEnabled) return show_403();
        $meta = $this->cfg->aiTxt;
        $lines = ["# ai.txt (experimental)", "site: ".rtrim($baseUrl,'/')];
        foreach($meta as $k=>$v) $lines[] = "{$k}: {$v}";
        $lines[] = ""; $lines[] = "[agents]";
        foreach ($this->robotsAiBlocks() as $agent=>$rows) {
            $lines[] = "agent: {$agent}";
            foreach($rows['allow'] as $p)    $lines[]="  allow: {$p}";
            foreach($rows['disallow'] as $p) $lines[]="  disallow: {$p}";
        }
        return implode("\n",$lines)."\n";
    }

    protected function match(string $pattern, string $path): bool
    {
        $rx = '#^'.str_replace(['*'],['.*'],preg_quote($pattern,'#')).'$#u';
        return (bool)preg_match($rx,$path);
    }
}
