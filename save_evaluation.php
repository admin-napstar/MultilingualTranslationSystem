
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

    $translationId = filter_var(
        $input['translation_id'] ?? null,
        FILTER_VALIDATE_INT
    );

    $correctedTranslation = trim(
        $input['corrected_translation'] ?? ''
    );

    if (!$translationId || $translationId < 1) {
        http_response_code(400);
        echo json_encode([
            'error' => 'Please select a valid translation.'
        ]);
        exit;
    }

    if ($correctedTranslation === '') {
        http_response_code(400);
        echo json_encode([
            'error' => 'Please enter your corrected translation.'
        ]);
        exit;
    }

    // Connect to the existing database.
    $database = new \Napstar\MultilingualTranslationSystem\Services\Database();
    $db = $database->getConnection();

    // Only allow the logged-in user to save a correction
    // for one of their own translations.
    $stmt = $db->prepare(
        'SELECT
            id,
            source_language_id,
            target_language_id,
            source_text,
            translated_text
         FROM translations
         WHERE id = :translation_id
           AND user_id = :user_id
         LIMIT 1'
    );

    $stmt->execute([
        'translation_id' => $translationId,
        'user_id' => (int) $_SESSION['user_id']
    ]);

    $translation = $stmt->fetch();

    if (!$translation) {
        http_response_code(404);
        echo json_encode([
            'error' => 'Translation not found in your history.'
        ]);
        exit;
    }

    // Save the machine translation and user's correction.
    $evaluation = new \Napstar\MultilingualTranslationSystem\Models\TranslationEvaluation();

    $evaluationId = $evaluation->create(
        (int) $_SESSION['user_id'],
        (int) $translation['source_language_id'],
        (int) $translation['target_language_id'],
        $translation['source_text'],
        $translation['translated_text'],
        $correctedTranslation
    );

    echo json_encode([
        'success' => true,
        'message' => 'Correction saved successfully.',
        'evaluation_id' => $evaluationId
    ]);

} catch (\Throwable $e) {
    error_log('Save evaluation error: ' . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'error' => 'Unable to save the correction. Please check the application error log.'
    ]);
}
