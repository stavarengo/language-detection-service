<?php

/** @var \Sta\LanguageDetectionService\ViewHelper $helper */

if (!isset($helper)) {
    // This request is access this site directly. Did not passed by index.php
    // Its like they type <http_address>/site.php in the browser address bar
    require_once __DIR__ . '/../vendor/autoload.php';
    $helper = new \Sta\LanguageDetectionService\ViewHelper();
    $helper->echoJson(
        $helper->apiProblem(
            'Not found',
            404,
            'This page does not exist.'
        ),
        404
    );
    return;
}

if (!isset($_POST['t']) && !isset($_GET['t'])) {
    $helper->echoJson(
        $helper->apiProblem(
            'Missing parameter',
            400,
            'Parameter "t" not set. You can send it either through query or post parameter.'
        ),
        400
    );

    return;
}

$text = trim(isset($_POST['t']) ? $_POST['t'] : $_GET['t']);

try {
    $detectLanguage     = new \Sta\Cld2PhpLanguageDetection\DetectLanguage();
    $detectionResponses = $detectLanguage->detect($text);
} catch (\Sta\Cld2PhpLanguageDetection\Exception\ModuleCld2NotFound $e) {
    $helper->echoJson($helper->apiProblem('Server misconfigured', 500, $e->getMessage()), 500);

    return;
}

$result = [];
foreach ($detectionResponses as $detectionResponse) {
    $result[] = [
        'code' => $detectionResponse->getLanguageCode(),
        'name' => $detectionResponse->getLanguageName(),
        'probability' => $detectionResponse->getProbability(),
        'confidence' => $detectionResponse->getConfidence(),
    ];
}

$helper->echoJson($result);
