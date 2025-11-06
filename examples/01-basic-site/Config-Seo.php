<?php
// Projendeki app/Config/Seo.php'ye benzer ama basit örnek
public string $siteName = 'Basic Site';
public array  $locales = ['tr-TR'];
public array  $rules = [
    ['pattern'=>'/search*','robots'=>'noindex,follow']
];
public array $sitemaps = [
    'pages' => [
        'type'=>'static',
        'items'=>[
            ['loc'=>'/','changefreq'=>'weekly','priority'=>1.0],
            ['loc'=>'/about','changefreq'=>'monthly','priority'=>0.6],
        ]
    ],
    'blog' => [
        'type'=>'callback',
        'callable'=>[\App\Models\BlogModel::class,'sitemapItems']
    ]
];
