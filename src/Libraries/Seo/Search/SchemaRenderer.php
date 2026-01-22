<?php

namespace ci4seopro\Libraries\Seo\Search;

class SchemaRenderer
{
  public function build(array $s): string
  {
    $json = [
      '@context' => 'https://schema.org',
      '@type' => 'WebPage',
      'name' => $s['__title'] ?? ($s['title'] ?? ''),
      'url' => $s['url'] ?? '',
      'inLanguage' => $s['locale'] ?? null,
    ];
    return '<script type="application/ld+json">' .
      json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) .
      '</script>' . "\n";
  }
}
