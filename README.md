# CodeIgniter 4 SEO Package

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D%208.1-blue.svg)](https://www.php.net/)
[![CodeIgniter Version](https://img.shields.io/badge/codeigniter-%3E%3D%204.0-blue.svg)](https://codeigniter.com/)

## Overview

ci4seopro is a comprehensive SEO solution for CodeIgniter 4 applications. It provides professional SEO features including sitemap generation, RSS feed creation, robots.txt management, and AI indexing policies.

## Features

- **Comprehensive Sitemap Generation**:

  - Standard sitemap with configurable chunking
  - News sitemap for Google News
  - Image sitemap for Google Images
  - Video sitemap for Google Videos

- **Robots.txt Management**:

  - Configurable allow/disallow rules
  - AI-specific policies
  - Automatic inclusion of sitemap references

- **RSS Feed Generation**:

  - Customizable RSS feed with items from your application

- **SEO Metadata Helper**:

  - Generates Open Graph and Twitter Card meta tags
  - Supports multilingual content
  - Configurable default values

- **AI/LLM Policies**:

  - Dedicated AI.txt endpoint
  - Configurable allow/disallow policies for AI crawlers

- **Caching System**:

  - HTTP caching with ETag and Last-Modified headers
  - Application-level caching for performance optimization

- **Command Line Tools**:
  - `seo:ping-sitemap` - Ping search engines with your sitemap
  - `seo:warm-cache` - Pre-generate and cache all SEO files

## Installation

You can install the package via Composer:

```bash
composer require bertugfahriozer/ci4seopro
```

## Configuration

After installation, publish the configuration file:

```bash
php spark publish:ci4seopro
```

This will create a `Seo.php` configuration file in your `app/Config` directory.

## Basic Usage

### 1. Configure the SEO settings

Edit the `app/Config/Seo.php` file to set your site information:

```php
public string $siteName = 'My Awesome E-commerce Site';
public string $siteDescription = 'Discover amazing products at great prices.';
public string $siteUrl = 'https://example.com';
public string $defaultLocale = 'en';
public array $locales = ['en', 'es', 'fr'];
public string $defaultImage = 'https://example.com/assets/og-default.jpg';
public string $twitterSite = '@example';
```

### 2. Add SEO meta tags to your views

In your view files, use the SEO helper to generate meta tags:

```php
<?= seo_meta([
    'title' => 'Product Page',
    'description' => 'Detailed information about our amazing product',
    'image' => 'https://example.com/products/product1.jpg',
    'type' => 'product'
]) ?>
```

### 3. Set up sitemap providers

For products and categories, create provider methods in your controllers:

```php
// In your ProductController
public function getProductsForSitemap(): array
{
    $products = $this->productModel->findAll();
    $items = [];

    foreach ($products as $product) {
        $items[] = [
            'loc' => site_url('products/' . $product['slug']),
            'images' => [
                [
                    'loc' => base_url('uploads/products/' . $product['image']),
                    'caption' => $product['name'],
                    'title' => $product['name'],
                ]
            ]
        ];
    }

    return $items;
}

// In your CategoryController
public function getCategoriesForSitemap(): array
{
    $categories = $this->categoryModel->findAll();
    $items = [];

    foreach ($categories as $category) {
        $items[] = [
            'loc' => site_url('categories/' . $category['slug']),
        ];
    }

    return $items;
}
```

Then update your SEO configuration:

```php
public array $sitemapProviders = [
    ['App\Controllers\ProductController::getProductsForSitemap'],
    ['App\Controllers\CategoryController::getCategoriesForSitemap'],
];
```

### 4. Set up RSS feed

Create a provider method for your RSS items:

```php
// In your ProductController
public function getProductsForRss(): array
{
    $products = $this->productModel->orderBy('created_at', 'DESC')->findAll(10);
    $items = [];

    foreach ($products as $product) {
        $items[] = [
            'title' => $product['name'],
            'link' => site_url('products/' . $product['slug']),
            'desc' => $product['description'],
            'date' => strtotime($product['created_at']),
            'author' => config(Seo::class)->contactEmail,
        ];
    }

    return $items;
}
```

Then update your SEO configuration:

```php
public bool $enableRss = true;
public string $rssTitle = 'My E-commerce Site RSS Feed';
public string $rssDescription = 'Latest products and updates from our e-commerce site';
public string $rssLanguage = 'en-US';
public $rssItemsProvider = 'App\Controllers\ProductController::getProductsForRss';
```

## Advanced Features

### AI Indexing Policies

Configure AI indexing policies in your SEO configuration:

```php
public bool $serveLlms = true;
public array $aiUserAgents = [
    'GPTBot',
    'CCBot',
    'ClaudeBot',
    'Claude-Web',
    'PerplexityBot',
    'Amazonbot',
    'Bytespider',
    'Google-Extended',
    'GoogleOther',
    'Meta-ExternalAgent'
];
public string $aiPolicy = 'Allow'; // or 'Disallow'
public string $contactEmail = 'webmaster@example.com';
```

### Caching

Configure caching behavior:

```php
public bool $httpCaching = true;
public int $httpCacheSeconds = 3600;
public bool $useAppCache = true;
public int $appCacheTTL = 3600;
```

### Command Line Tools

Run the following commands to manage your SEO files:

```bash
# Ping search engines with your sitemap
php spark seo:ping-sitemap

# Warm caches (pre-generate all SEO files)
php spark seo:warm-cache
```

## E-commerce Specific Features

For e-commerce sites, the package includes special features:

1. **Product Image Sitemap**: Automatically includes product images in your image sitemap
2. **Product RSS Feed**: Keeps your customers updated with new products
3. **Category Sitemap**: Ensures all product categories are properly indexed
4. **Rich Product Metadata**: Enhanced metadata for better search visibility

## Best Practices

1. **Regularly Update Your Sitemap**: New products and categories should be added to your sitemap regularly
2. **Monitor Your RSS Feed**: Ensure it's being properly consumed by feed readers
3. **Keep Your SEO Configuration Updated**: As your site evolves, update your SEO settings accordingly
4. **Test Your AI Policies**: Verify that your AI indexing policies are working as expected

## Support

If you encounter any issues or have questions, please open an issue on the [GitHub repository](https://github.com/bertugfahriozer/ci4seopro).

## License

The ci4seopro package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
