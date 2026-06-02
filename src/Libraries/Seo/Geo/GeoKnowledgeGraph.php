<?php
declare(strict_types=1);

namespace ci4seopro\Libraries\Seo\Geo;

use ci4seopro\Config\Seo;

class GeoKnowledgeGraph
{
    public function build(Seo $cfg): array
    {
        $org = $cfg->geoOrganization;
        if (empty($org['name'])) return [];

        $schema = [
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => $org['name'],
            'url'      => $org['url'] ?? '',
            'sameAs'   => $org['sameAs'] ?? [],
        ];

        if (!empty($org['logo'])) {
            $schema['logo'] = ['@type' => 'ImageObject', 'url' => $org['logo']];
        }

        if (!empty($org['founder'])) {
            $schema['founder'] = ['@type' => 'Person', 'name' => $org['founder']];
        }

        return $schema;
    }
}
