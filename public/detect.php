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

$text             = trim(isset($_POST['t']) ? $_POST['t'] : $_GET['t']);
$onlyMostProbably = array_key_exists('most-probably', $_GET);
$normalizeText    = !array_key_exists('do-not-normalize-text', $_GET);

$convertDetectionResponseToArray = function (\Sta\Cld2PhpLanguageDetection\DetectionResult $detectionResponse) {
    return [
        'code' => $detectionResponse->getLanguageCode(),
        'name' => $detectionResponse->getLanguageName(),
        'probability' => $detectionResponse->getProbability(),
        'confidence' => $detectionResponse->getConfidence(),
    ];
};

$result = [];
try {
    $detectLanguage = new \Sta\Cld2PhpLanguageDetection\DetectLanguage();
    if ($onlyMostProbably) {
        if ($detectionResponse = $detectLanguage->detectOnlyMostProbably($text, $normalizeText)) {
            $result = $convertDetectionResponseToArray($detectionResponse);
        }
    } else {
        $result             = [];
        $detectionResponses = $detectLanguage->detect($text, $normalizeText);
        foreach ($detectionResponses as $detectionResponse) {
            $result[] = $convertDetectionResponseToArray($detectionResponse);
        }
    }
} catch (\Sta\Cld2PhpLanguageDetection\Exception\ModuleCld2NotFound $e) {
    $helper->echoJson($helper->apiProblem('Server misconfigured', 500, $e->getMessage()), 500);

    return;
}

$helper->echoJson($result);
