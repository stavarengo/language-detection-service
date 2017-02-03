<?php

/** @var \Sta\LanguageDetectionService\ViewHelper $helper */

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
    $detectionResponses = $detectLanguage->detect($_POST['t']);
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
