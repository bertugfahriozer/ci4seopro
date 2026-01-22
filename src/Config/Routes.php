<?php

// Robots
$routes->get('robots.txt', '\ci4seopro\Controllers\Search\RobotsController::index');
// Sitemap INDEX ve parçalar
$routes->get('sitemap.xml', '\ci4seopro\Controllers\Search\SitemapController::index');
$routes->get('sitemap-(:segment).xml', '\ci4seopro\Controllers\Search\SitemapController::chunk/$1');
// AI
$routes->get('.well-known/ai.txt', '\ci4seopro\Controllers\Ai\AiTxtController::index');
$routes->get('llms.txt', '\ci4seopro\Controllers\Ai\AiTxtController::index'); // alias
$routes->get('api/ai/context', '\ci4seopro\Controllers\Ai\AiApiController::context');
// FEEDS
$routes->get('feed-(:segment).xml', '\ci4seopro\Controllers\Feed\FeedController::show/$1'); // rss2/atom
$routes->get('feed-(:segment).json', '\ci4seopro\Controllers\Feed\FeedController::show/$1'); // jsonfeed/llm-changefeed

// Özel doğrulama HTML dosyaları için (örn: googleXXXX.html)
$routes->get('(:segment).html', 'Search\VerificationController::html/$1');
// .well-known/* için
$routes->get('.well-known/(:segment)', 'Search\VerificationController::wellKnown/$1');
$routes->get('seo/health', 'Seo\HealthController::index');
