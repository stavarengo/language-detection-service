<?php

$start = time();

require_once __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/config.php';
$helper = new \Sta\LanguageDetectionService\ViewHelper();

register_shutdown_function(
    function () use ($start, $config, $helper) {
        $helper->trackTimeOnAnalytics($config, time() - $start);
    }
);

$helper->trackRequestOnAnalytics($config);

if (strpos($helper->getRequestUri(), $helper->getBasePath('/detect')) === 0) {
    require __DIR__ . '/./detect.php';
} else {
    require __DIR__ . '/./site.php';
}
