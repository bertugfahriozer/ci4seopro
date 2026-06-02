<?php
declare(strict_types=1);

namespace ci4seopro\Libraries\Seo\Geo;

use ci4seopro\Config\Seo;
use ci4seopro\Libraries\Seo\Ai\AiPolicy;

class GeoLlmBuilder
{
    public function __construct(protected Seo $cfg) {}

    public function llmsTxtBody(string $baseUrl): string
    {
        // aiEnabled = false iken AiPolicy::aiTxtBody() show_403() fırlatır; guard şart
        if ($this->cfg->aiEnabled) {
            $policy = new AiPolicy($this->cfg);
            $v1     = $policy->aiTxtBody($baseUrl);
        } else {
            $v1 = "# ai.txt\nsite: " . rtrim($baseUrl, '/') . "\n";
        }

        if (!$this->cfg->geoEnabled) return $v1;

        $llms = $this->cfg->geoLlmsTxt;
        if (empty($llms['title']) && empty($llms['sections'])) return $v1;

        $lines = ['', '# ' . ($llms['title'] ?? 'Content Map')];
        if (!empty($llms['description'])) {
            $lines[] = $llms['description'];
        }

        foreach ($llms['sections'] ?? [] as $section) {
            $lines[] = '';
            $lines[] = '## ' . ($section['title'] ?? '');
            foreach ($section['links'] ?? [] as $link) {
                $url  = rtrim($baseUrl, '/') . ($link['url'] ?? '');
                $line = '- [' . ($link['title'] ?? '') . '](' . $url . ')';
                if (!empty($link['description'])) {
                    $line .= ': ' . $link['description'];
                }
                $lines[] = $line;
            }
        }

        return $v1 . implode("\n", $lines) . "\n";
    }

    public function chunkHtml(string $html): array
    {
        if (stripos($html, '<body') !== false) {
            preg_match('/<body[^>]*>(.*?)<\/body>/is', $html, $m);
            $html = $m[1] ?? $html;
        }

        $parts = preg_split('/(<h[23][^>]*>.*?<\/h[23]>)/is', $html, -1, PREG_SPLIT_DELIM_CAPTURE);

        $chunks         = [];
        $currentHeading = 'intro';
        $currentText    = '';

        foreach ($parts as $part) {
            if (preg_match('/<h[23][^>]*>(.*?)<\/h[23]>/is', $part, $m)) {
                $text = trim(strip_tags($currentText));
                if ($text !== '') {
                    $chunks = array_merge($chunks, $this->splitBySize($currentHeading, $text));
                }
                $currentHeading = trim(strip_tags($m[1]));
                $currentText    = '';
            } else {
                $currentText .= $part;
            }
        }

        $text = trim(strip_tags($currentText));
        if ($text !== '') {
            $chunks = array_merge($chunks, $this->splitBySize($currentHeading, $text));
        }

        return $chunks;
    }

    public function aiPluginManifest(string $baseUrl): array
    {
        $m    = $this->cfg->geoManifest;
        $name = $m['name'] ?? $this->cfg->siteName;

        return [
            'schema_version'        => 'v1',
            'name_for_human'        => $name,
            'name_for_model'        => strtolower(preg_replace('/\s+/', '_', $name)),
            'description_for_human' => $m['description'] ?? '',
            'description_for_model' => $m['description'] ?? '',
            'auth'                  => ['type' => 'none'],
            'api'                   => [
                'type' => 'openapi',
                'url'  => rtrim($baseUrl, '/') . ($m['api_url'] ?? '/api/geo') . '/openapi.json',
            ],
            'contact_email'  => $m['contact_email'] ?? '',
            'legal_info_url' => rtrim($baseUrl, '/') . '/privacy',
        ];
    }

    protected function splitBySize(string $heading, string $text): array
    {
        $limit = $this->cfg->geoChunkSize * 4; // ~4 chars per token

        if (mb_strlen($text) <= $limit) {
            return [['heading' => $heading, 'text' => $text, 'tokens' => (int)(mb_strlen($text) / 4)]];
        }

        $chunks  = [];
        $words   = explode(' ', $text);
        $current = '';

        foreach ($words as $word) {
            if ($current !== '' && mb_strlen($current) + 1 + mb_strlen($word) > $limit) {
                $chunks[] = [
                    'heading' => $heading,
                    'text'    => trim($current),
                    'tokens'  => (int)(mb_strlen($current) / 4),
                ];
                $current = $word;
            } else {
                $current .= ($current === '' ? '' : ' ') . $word;
            }
        }

        if ($current !== '') {
            $chunks[] = [
                'heading' => $heading,
                'text'    => trim($current),
                'tokens'  => (int)(mb_strlen($current) / 4),
            ];
        }

        return $chunks;
    }
}
