<?php
$routes->get('robots.txt', '\\bertugfahriozer\\ci4SeoPro\\Controllers\\SeoController::robots');
$routes->get('sitemap.xml', '\\bertugfahriozer\\ci4SeoPro\\Controllers\\SeoController::sitemapIndex');
$routes->get('sitemaps/(:num).xml', '\\bertugfahriozer\\ci4SeoPro\\Controllers\\SeoController::sitemap/$1');
$routes->get('sitemap-news.xml', '\\bertugfahriozer\\ci4SeoPro\\Controllers\\SeoController::sitemapNews');
$routes->get('sitemap-images.xml', '\\bertugfahriozer\\ci4SeoPro\\Controllers\\SeoController::sitemapImages');
$routes->get('sitemap-videos.xml', '\\bertugfahriozer\\ci4SeoPro\\Controllers\\SeoController::sitemapVideos');
$routes->get('rss.xml', '\\bertugfahriozer\\ci4SeoPro\\Controllers\\SeoController::rss');
$routes->get('llms.txt', '\\bertugfahriozer\\ci4SeoPro\\Controllers\\SeoController::llms');
$routes->get('ai.txt', '\\bertugfahriozer\\ci4SeoPro\\Controllers\\SeoController::llms');
