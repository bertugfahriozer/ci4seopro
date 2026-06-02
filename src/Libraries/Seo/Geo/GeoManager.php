<?php
declare(strict_types=1);

namespace ci4seopro\Libraries\Seo\Geo;

use ci4seopro\Config\Seo;

class GeoManager
{
    protected array $state        = [];
    protected array $faqSchemas   = [];
    protected array $howToSchemas = [];
    protected array $claimSchemas = [];

    public function __construct(protected Seo $config) {}

    public function isEnabled(): bool
    {
        return $this->config->geoEnabled;
    }

    public function set(string $k, mixed $v): self
    {
        $this->state[$k] = $v;
        return $this;
    }

    public function addFaq(array $faqs): self
    {
        $this->faqSchemas[] = $faqs;
        return $this;
    }

    public function addHowTo(array $data): self
    {
        $this->howToSchemas[] = $data;
        return $this;
    }

    public function addClaimReview(array $claim): self
    {
        $this->claimSchemas[] = $claim;
        return $this;
    }

    public function renderHead(): string
    {
        if (!$this->isEnabled()) return '';

        $renderer = new GeoRenderer();
        $url      = $this->state['url'] ?? current_url();
        $out      = '';

        $out .= $renderer->buildKnowledgeGraph($this->config, $this->state);
        $out .= $renderer->buildSpeakable($this->config, $url);
        $out .= $renderer->buildEeat($this->state);

        foreach ($this->faqSchemas as $faqs) {
            $schema = GeoSchemaPreset::faqPage($faqs);
            $out .= '<script type="application/ld+json">'
                . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                . '</script>' . "\n";
        }

        foreach ($this->howToSchemas as $data) {
            $schema = GeoSchemaPreset::howTo($data);
            $out .= '<script type="application/ld+json">'
                . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                . '</script>' . "\n";
        }

        foreach ($this->claimSchemas as $claim) {
            $schema = GeoSchemaPreset::claimReview($claim);
            $out .= '<script type="application/ld+json">'
                . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                . '</script>' . "\n";
        }

        return $out;
    }

    public function injectIntoHtml(?string $html): string
    {
        if (!$this->isEnabled()) return $html ?? '';
        $head = $this->renderHead();
        if ($head === '') return $html ?? '';
        $pos = stripos((string)$html, '</head>');
        return $pos !== false
            ? substr($html, 0, $pos) . $head . substr($html, $pos)
            : $head . ($html ?? '');
    }
}
