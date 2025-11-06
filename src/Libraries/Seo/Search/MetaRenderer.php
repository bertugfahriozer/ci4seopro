<?php

namespace bertugfahriozer\ci4seopro\Libraries\Seo\Search;

class MetaRenderer
{
    public function build(array $s): string
    {
        $t = $s['__title'] ?? ($s['title'] ?? $s['site'] ?? '');
        $d = $s['__desc']  ?? ($s['excerpt'] ?? '');
        $lines = [];
        $lines[] = '<title>' . esc($t) . '</title>';
        $lines[] = '<meta name="description" content="' . esc($d) . '">';
        if (!empty($s['canonical'])) $lines[] = '<link rel="canonical" href="' . esc($s['canonical']) . '">';
        if (!empty($s['robots']))    $lines[] = '<meta name="robots" content="' . esc($s['robots']) . '">';
        $lines[] = '<meta property="og:title" content="' . esc($t) . '">';
        $lines[] = '<meta property="og:description" content="' . esc($d) . '">';
        $lines[] = '<meta property="og:url" content="' . esc($s['url'] ?? '') . '">';
        $lines[] = '<meta name="twitter:card" content="summary_large_image">';
        return implode("\n", $lines) . "\n";
    }
}
