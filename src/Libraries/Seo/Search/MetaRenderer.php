<?php

namespace ci4seopro\Libraries\Seo\Search;

use ci4seopro\Config\Seo;

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
        $lines[] = '<meta property="og:type" content="website">';
        $lines[] = '<meta property="og:locale" content="' . esc($this->formatLocale($s['locale']) ?? 'tr_TR') . '">';
        $lines[] = '<meta property="og:site_name" content="' . esc($s['site'] ?? '') . '">';
        $lines[] = '<meta property="og:image" content="' . esc($s['image'] ?? '') . '">';
        $lines[] = '<meta name="twitter:card" content="summary_large_image">';
        $lines[] = '<meta name="twitter:title" content="' . esc($t) . '">';
        $lines[] = '<meta name="twitter:description" content="' . esc($d) . '">';
        $lines[] = '<meta name="twitter:image" content="' . esc($s['image'] ?? '') . '">';
        if (!empty($s['keywords'])) $lines[] = '<meta name="keywords" content="' . esc(implode(',', $s['keywords'])) . '">';
        if(!empty($s['author']))  $lines[] = '<meta name="author" content="' . esc($s['author']) . '">';

        // Verification meta tags (by host)
        $host = parse_url($s['url'] ?? '', PHP_URL_HOST) ?: ($_SERVER['HTTP_HOST'] ?? '');
        $cfg  = new Seo;
        $metaSets = [];
        // '*' + host birleşimi
        if (!empty($cfg->verify['meta']['*']))      $metaSets[] = $cfg->verify['meta']['*'];
        if ($host && !empty($cfg->verify['meta'][$host])) $metaSets[] = $cfg->verify['meta'][$host];
        foreach ($metaSets as $set) {
            foreach ($set as $name => $token) {
                if ($token !== '') {
                    $lines[] = '<meta name="' . esc($name) . '" content="' . esc($token) . '">';
                }
            }
        }
        return implode("\n", $lines) . "\n";
    }
    /**
     * Locale'i POSIX formatına (tr_TR) çevir
     * tr → tr_TR
     * en → en_US
     * de → de_DE
     * vb.
     */
    protected function formatLocale(string $locale): string
    {
        // Eğer zaten formatlanmış ise (tr_TR), olduğu gibi döndür
        if (str_contains($locale, '_')) {
            return $locale;
        }

        // Locale haritalandırması
        $localeMap = [
            'tr' => 'tr_TR',
            'en' => 'en_US',
            'de' => 'de_DE',
            'fr' => 'fr_FR',
            'es' => 'es_ES',
            'it' => 'it_IT',
            'pt' => 'pt_BR',
            'ru' => 'ru_RU',
            'ja' => 'ja_JP',
            'zh' => 'zh_CN',
            'ar' => 'ar_SA',
        ];
        return $localeMap[$locale] ?? $locale . '_' . strtoupper($locale);
    }
}
