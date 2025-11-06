## 02 - Multilang Blog

- `Config/Seo.php` içinde `locales = ['tr-TR','en-US']`
- `sitemaps['blog']['hreflang'] = true`
- Blog modeliniz `sitemapItems()` döndürürken `alternates` alanını eklesin:
  ```php
  return [
    ['loc'=>'/tr/blog/yazi-1','lastmod'=>'2025-11-01','alternates'=>[
       ['lang'=>'tr','loc'=>'/tr/blog/yazi-1'],
       ['lang'=>'en','loc'=>'/en/blog/post-1'],
    ]],
  ];
  ```
