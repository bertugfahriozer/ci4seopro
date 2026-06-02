<?php

// Robots
$routes->get('robots.txt', '\ci4seopro\Controllers\Search\RobotsController::index');
// Sitemap INDEX ve parçalar
$routes->get('sitemap.xml', '\ci4seopro\Controllers\Search\SitemapController::index');
$routes->get('sitemap-(:segment).xml', '\ci4seopro\Controllers\Search\SitemapController::chunk/$1');

// AI / GEO — llms.txt ve ai.txt GEO etkinse v2 endpoint'e yönlenir
if ((new \ci4seopro\Config\Seo())->geoEnabled) {
    $routes->get('.well-known/ai.txt',         '\ci4seopro\Controllers\Geo\GeoLlmController::llmsTxt');
    $routes->get('llms.txt',                   '\ci4seopro\Controllers\Geo\GeoLlmController::llmsTxt');
    $routes->get('api/geo/chunk',              '\ci4seopro\Controllers\Geo\GeoLlmController::chunk');
    $routes->get('.well-known/ai-plugin.json', '\ci4seopro\Controllers\Geo\GeoManifestController::plugin');
} else {
    $routes->get('.well-known/ai.txt', '\ci4seopro\Controllers\Ai\AiTxtController::index');
    $routes->get('llms.txt',           '\ci4seopro\Controllers\Ai\AiTxtController::index');
}
$routes->get('api/ai/context', '\ci4seopro\Controllers\Ai\AiApiController::context');

// FEEDS
$routes->get('feed-(:segment).xml',  '\ci4seopro\Controllers\Feed\FeedController::show/$1');
$routes->get('feed-(:segment).json', '\ci4seopro\Controllers\Feed\FeedController::show/$1');

// Doğrulama — catch-all well-known'dan önce spesifik rotalar tanımlandı (yukarıda)
$routes->get('(:segment).html',        '\ci4seopro\Controllers\Search\VerificationController::html/$1');
$routes->get('.well-known/(:segment)', '\ci4seopro\Controllers\Search\VerificationController::wellKnown/$1');
$routes->get('seo/health',             '\ci4seopro\Controllers\Search\HealthController::index');
