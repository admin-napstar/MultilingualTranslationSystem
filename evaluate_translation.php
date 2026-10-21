
<?php

session_start();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'error' => 'Please log in again.'
    ]);
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

use Napstar\MultilingualTranslationSystem\Services\TranslationService;

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            'error' => 'POST requests only.'
        ]);
        exit;
    }

    $input = json_decode(
        file_get_contents('php://input'),
        true
    );

    if (!is_array($input)) {
        http_response_code(400);
        echo json_encode([
            'error' => 'Invalid request data.'
        ]);
        exit;
    }

    $sourceText = trim($input['text'] ?? '');
    $sourceLanguage = trim($input['source_language'] ?? '');
    $targetLanguage = trim($input['target_language'] ?? '');

    if (
        $sourceText === '' ||
        $sourceLanguage === '' ||
        $targetLanguage === ''
    ) {
        http_response_code(400);
        echo json_encode([
            'error' => 'Text and both language codes are required.'
        ]);
        exit;
    }

    $service = new TranslationService();

    $result = $service->translateWithConfidence(
        $sourceText,
        $sourceLanguage,
        $targetLanguage
    );

    echo json_encode($result);

} catch (\Throwable $e) {
    error_log('Translation evaluation error: ' . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'error' => 'Unable to evaluate the translation. Check that the translation API is running.'
    ]);
}
