## 03 - E-commerce

- Kategori filtreleri/parametreleri noindex:
  ```php
  $rules[] = ['pattern'=>'/category*','robots'=>'noindex,follow'];
  ```
- Sitemaps:
  ```php
  $sitemaps['products'] = ['type'=>'callback','callable'=>[ProductModel::class,'sitemapItems']];
  $sitemaps['categories'] = ['type'=>'callback','callable'=>[CategoryModel::class,'sitemapItems']];
  ```
- Ürün stok=0 ise sayfa bazlı `noindex` kuralını controller veya `rules` ile uygulayın.
