## 04 - News + Image + Video sitemaps

- `SitemapBuilder` içine ek namespace ve node üreticileri ekleyerek (örn. `xmlns:news`, `xmlns:image`, `xmlns:video`)
  ayrı parçalar üretin: `/sitemap-news.xml`, `/sitemap-images.xml`, `/sitemap-videos.xml`
- Örnek item yapıları:
  ```php
  // image example inside url:
  'images' => [
    ['loc'=>'/uploads/p1.jpg','caption'=>'Ürün fotoğrafı','title'=>'P1'],
  ]
  // video example:
  'video' => [
    ['thumbnail_loc'=>'/thumbs/v1.jpg','title'=>'Video Başlık','description'=>'Kısa açıklama','content_loc'=>'/videos/v1.mp4']
  ]
  // news example:
  'news' => ['publication_date'=>'2025-11-06','title'=>'Haber Başlık','publication_name'=>'My News']
  ```
