<?php
declare(strict_types=1);

namespace ci4seopro\Libraries\Seo\Geo;

use ci4seopro\Config\Seo;

class GeoRenderer
{
    public function buildKnowledgeGraph(Seo $cfg, array $state): string
    {
        $schema = (new GeoKnowledgeGraph())->build($cfg);
        if (empty($schema)) return '';
        return '<script type="application/ld+json">'
            . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            . '</script>' . "\n";
    }

    public function buildSpeakable(Seo $cfg, string $url): string
    {
        if (empty($cfg->geoSpeakable)) return '';
        $schema = GeoSchemaPreset::speakable($url, $cfg->geoSpeakable);
        return '<script type="application/ld+json">'
            . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            . '</script>' . "\n";
    }

    public function buildEeat(array $state): string
    {
        $lines = [];
        if (!empty($state['author'])) {
            $lines[] = '<meta name="author" content="' . esc($state['author']) . '">';
        }
        if (!empty($state['datePublished'])) {
            $lines[] = '<meta name="article:published_time" content="' . esc($state['datePublished']) . '">';
        }
        if (!empty($state['dateModified'])) {
            $lines[] = '<meta name="article:modified_time" content="' . esc($state['dateModified']) . '">';
        }
        return empty($lines) ? '' : implode("\n", $lines) . "\n";
    }
}
