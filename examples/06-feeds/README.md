## 06 - Feeds

Routes:
```php
$routes->get('feed-(:segment).xml', 'Feed\FeedController::show/$1');
$routes->get('feed-(:segment).json', 'Feed\FeedController::show/$1');
```
Config `feeds` tanımı:
```php
'blog-rss' => ['format'=>'rss2','type'=>'callback','callable'=>[BlogModel::class,'feedItems'], 'title'=>'Blog (RSS)','link'=>'/blog','desc'=>'Blog yazıları']
```
Model örneği:
```php
public static function feedItems(): array {
  return [
    ['id'=>'post-1','url'=>'/blog/post-1','title'=>'Yazı 1','summary'=>'Özet 1','date'=>'2025-11-01'],
    ['id'=>'post-2','url'=>'/blog/post-2','title'=>'Yazı 2','summary'=>'Özet 2','date'=>'2025-11-03'],
  ];
}
```
LLM changefeed (JSON):
```php
'llm-changefeed' => ['format'=>'json','type'=>'callback','callable'=>[ChangefeedModel::class,'items']]
```
