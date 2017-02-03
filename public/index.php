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

$requestPath = parse_url($helper->getRequestUri(), PHP_URL_PATH);

if ($requestPath == $helper->getBasePath('/detect')) {
    require __DIR__ . '/./detect.php';
} else if ($requestPath == $helper->getBasePath('/')) {
    require __DIR__ . '/./site.php';
} else {
    $helper->echoJson(
        $helper->apiProblem(
            'Not found',
            404,
            'This page does not exist.'
        ),
        404
    );
}
