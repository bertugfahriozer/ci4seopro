<?php

namespace ci4seopro\Libraries\Seo\Search;

use ci4seopro\Libraries\Seo\Search\MetaRenderer;
use ci4seopro\Libraries\Seo\Search\SchemaRenderer;
use ci4seopro\Config\Seo;

class SeoManager
{
    protected array $state = [];
    protected array $customSchemas = [];

    public function __construct(protected Seo $config)
    {
        $base = rtrim($config->baseUrl ?: site_url('/'), '/');
        $this->state = [
            'site'     => $config->siteName,
            'url'      => current_url() ?: $base,
            'locale'   => service('request')->getLocale() ?? $config->defaultLocale,
            'canonical' => current_url(),
            'keywords' => [],
        ];
    }

    public function set(string $k, $v): self
    {
        $this->state[$k] = $v;
        return $this;
    }
    public function title(?string $t = null): string
    {
        if ($t !== null) $this->state['title'] = $t;
        return $this->state['__title'] ?? ($this->state['title'] ?? $this->config->siteName);
    }
    public function description(?string $d = null): string
    {
        if ($d !== null) $this->state['excerpt'] = $d;
        return $this->state['__desc'] ?? ($this->state['excerpt'] ?? '');
    }
    public function keywords($kw = []): self
    {
        if ($kw) {
            $this->state['keywords'] = is_array($kw) ? $kw : array_map('trim', explode(',', $kw));
        }
        return $this;
    }

    public function renderHead(): string
    {
        $this->applyRules();
        $this->renderTemplates();
        $meta   = (new MetaRenderer())->build($this->state);
        $schema = (new SchemaRenderer())->build($this->state);
        foreach ($this->customSchemas as $c) {
            $schema .= '<script type="application/ld+json">' . json_encode($c, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
        }
        return $meta . $schema;
    }

    public function injectIntoHtml(?string $html='', array $ctx = []): string
    {
        $this->state = array_merge($this->state, $ctx);
        $head = $this->renderHead();
        $pos = stripos($html, '</head>');
        return $pos !== false ? substr($html, 0, $pos) . $head . substr($html, $pos) : $head . $html;
    }

    protected function applyRules(): void
    {
        $path = parse_url($this->state['url'], PHP_URL_PATH) ?: '/';
        foreach ($this->config->rules as $r) {
            if ($this->match($r['pattern'], $path)) {
                if (!empty($r['robots']))    $this->state['robots'] = $r['robots'];
                if (!empty($r['canonical'])) $this->state['canonical'] = $r['canonical'];
            }
        }
    }

    protected function renderTemplates(): void
    {
        $tpl = $this->config->templates['default'];
        $data = [
            'site' => $this->config->siteName,
            'title' => $this->state['title'] ?? '',
            'excerpt' => $this->state['excerpt'] ?? '',
            'tagline' => $this->state['tagline'] ?? '',
            'summary' => $this->state['summary'] ?? '',
        ];
        $this->state['__title'] = $this->tokenize($tpl['title'], $data);
        $this->state['__desc']  = $this->tokenize($tpl['desc'], $data);
    }

    public function sitemapXml(string $name): string
    {
        $builder = new SitemapBuilder($this->config);
        return $builder->buildChunk($name);
    }

    public function sitemapIndex(): string
    {
        $builder = new SitemapBuilder($this->config);
        return $builder->buildIndex();
    }

    protected function tokenize(string $tpl, array $data): string
    {
        return preg_replace_callback('/\{([a-z0-9_]+)(?:\.(\d+))?\}/i', function ($m) use ($data) {
            $k = $m[1];
            $lim = $m[2] ?? null;
            $v = strip_tags((string)($data[$k] ?? ''));
            return $lim ? mb_substr($v, 0, (int)$lim) : $v;
        }, $tpl);
    }

    protected function match(string $pattern, string $path): bool
    {
        $rx = '#^' . str_replace(['*'], ['.*'], preg_quote($pattern, '#')) . '$#u';
        return (bool)preg_match($rx, $path);
    }

    public function addSchema(array $jsonld): self
    {
        $this->customSchemas[] = $jsonld;
        return $this;
    }
}
