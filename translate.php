<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

use Napstar\MultilingualTranslationSystem\Controllers\TranslationController;

$controller = new TranslationController();

$languages = $controller->getLanguages();

$translatedText = '';
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        $sourceLanguageId =
            (int) ($_POST['source_language'] ?? 0);

        $targetLanguageId =
            (int) ($_POST['target_language'] ?? 0);

        $sourceText =
            $_POST['source_text'] ?? '';

        $translatedText =
            $controller->translate(
                $_SESSION['user_id'],
                $sourceLanguageId,
                $targetLanguageId,
                $sourceText
            );

    } catch (Exception $e) {

        $error = $e->getMessage();
    }
}

$pageTitle = 'Translate';

require __DIR__ . '/views/layouts/header.php';

require __DIR__ . '/views/translation/index.php';

require __DIR__ . '/views/layouts/footer.php';