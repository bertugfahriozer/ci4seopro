# Kullanım Talimatı (Detaylı)

## 1) Dosyaları Kopyala
- `app/` klasörünü CI4 projenizin köküne kopyalayın.

## 2) Filters Kaydı
`app/Config/Filters.php` içinde global after filtrelerini ekleyin:
```php
public array $globals = [
    'before' => [],
    'after'  => [
        \App\Filters\SearchSeoFilter::class,
        \App\Filters\AiHeaderFilter::class,
    ],
];
```

## 3) Routes
`app/Config/Routes.php`:
```php
// Robots
$routes->get('robots.txt', 'Search\RobotsController::index');
// Sitemap INDEX ve parçalar
$routes->get('sitemap.xml', 'Search\SitemapController::index');
$routes->get('sitemap-(:segment).xml', 'Search\SitemapController::chunk/$1');
// AI
$routes->get('.well-known/ai.txt', 'Ai\AiTxtController::index');
$routes->get('llms.txt', 'Ai\AiTxtController::index'); // alias
$routes->get('api/ai/context', 'Ai\AiApiController::context');
// FEEDS
$routes->get('feed-(:segment).xml', 'Feed\FeedController::show/$1'); // rss2/atom
$routes->get('feed-(:segment).json', 'Feed\FeedController::show/$1'); // jsonfeed/llm-changefeed
```

## 4) Config Düzenle
`app/Config/Seo.php` içindeki alanları düzenleyin (siteName, templates, rules, sitemaps, aiHeaderRules, aiTxt, aiAgents, feeds).

## 5) View Kullanımı
```php
<title><?= seo_title() ?></title>
<?= seo_head() ?>
```
Controller’da:
```php
service('seosearch')->set('title','Blog Yazısı')->set('excerpt','Kısa açıklama');
```

## 6) Sitemaps
- Index: `/sitemap.xml`
- Parçalar: `/sitemap-pages.xml`, `/sitemap-blog.xml` vb.

## 7) AI/LLM
- `/.well-known/ai.txt` : politika dosyası
- `/api/ai/context?url=/ornek` : sade içerik endpoint

## 8) FEEDS (RSS/Atom/JSON Feed + LLM Changefeed)
`Config->feeds` ile birden fazla feed tanımı yapın (ör. `blog-rss`, `blog-atom`, `blog-json`, `llm-changefeed`).  
Model callback `feedItems()` örnek dönüş:
```php
return [
  ['id'=>'post-1','url'=>'/blog/post-1','title'=>'Yazı 1','summary'=>'Özet','date'=>'2025-11-06'],
  // JSON Feed için 'content' ekleyebilirsiniz
];
```
