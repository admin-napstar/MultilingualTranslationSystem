
<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpWord\IOFactory;
use Smalot\PdfParser\Parser;

function redirectWithError(string $message): void
{
    $_SESSION['document_status'] = $message;
    header('Location: document_translate.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectWithError('Invalid request.');
}

if (
    !isset($_FILES['document']) ||
    $_FILES['document']['error'] !== UPLOAD_ERR_OK
) {
    redirectWithError('Please select a valid document to upload.');
}

$file = $_FILES['document'];

$maxSize = 10 * 1024 * 1024;

if ($file['size'] <= 0 || $file['size'] > $maxSize) {
    redirectWithError('The document must be smaller than 10 MB.');
}

$originalName = basename($file['name']);
$extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

if (!in_array($extension, ['txt', 'docx', 'pdf'], true)) {
    redirectWithError('Only TXT, DOCX, and PDF documents are supported.');
}

$targetLanguages = [
    'Sesotho'   => 'sot_Latn',
    'isiZulu'   => 'zul_Latn',
    'isiXhosa'  => 'xho_Latn',
    'Setswana'  => 'tsn_Latn',
    'Sepedi'    => 'nso_Latn',
    'itsonga'   => 'tso_Latn',
    'Tshivenda' => 'ven_Latn',
    'Siswati'   => 'ssw_Latn',
    'isiNdebele'=> 'nbl_Latn',
];

$targetLanguage = $_POST['target_language'] ?? '';

if (!isset($targetLanguages[$targetLanguage])) {
    redirectWithError('Please select a supported target language.');
}

// The source language is always English.
$sourceLanguageCode = 'eng_Latn';
$targetLanguageCode = $targetLanguages[$targetLanguage];

// --------------------------------------------------
// Extract text from the uploaded document
// --------------------------------------------------

try {
    if ($extension === 'txt') {
        $text = file_get_contents($file['tmp_name']);

        if ($text === false) {
            redirectWithError('Could not read the text document.');
        }
    } elseif ($extension === 'docx') {
        $word = IOFactory::load($file['tmp_name']);
        $text = '';

        foreach ($word->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . "\n";
                } elseif (method_exists($element, 'getElements')) {
                    foreach ($element->getElements() as $child) {
                        if (method_exists($child, 'getText')) {
                            $text .= $child->getText() . "\n";
                        }
                    }
                }
            }
        }
    } else {
        $parser = new Parser();
        $pdf = $parser->parseFile($file['tmp_name']);
        $text = $pdf->getText();
    }
} catch (Throwable $e) {
    error_log('Document extraction error: ' . $e->getMessage());
    redirectWithError('Could not read the document. Check that the file is valid.');
}

$text = trim($text);

if ($text === '') {
    redirectWithError(
        'No readable text was found. Scanned PDFs and image-only documents are not supported yet.'
    );
}

// --------------------------------------------------
// Split the text into manageable chunks
// --------------------------------------------------

$chunks = preg_split('/(?<=[.!?])\s+|\R+/', $text, -1, PREG_SPLIT_NO_EMPTY);

if (!$chunks) {
    $chunks = [$text];
}

$translationChunks = [];
$currentChunk = '';

foreach ($chunks as $chunk) {
    $chunk = trim($chunk);

    if ($chunk === '') {
        continue;
    }

    // Keep requests reasonably small for the translation model.
    while (mb_strlen($chunk, 'UTF-8') > 1000) {
        $translationChunks[] = mb_substr($chunk, 0, 1000, 'UTF-8');
        $chunk = mb_substr($chunk, 1000, null, 'UTF-8');
    }

    if ($currentChunk !== '' &&
        mb_strlen($currentChunk . ' ' . $chunk, 'UTF-8') > 1000) {
        $translationChunks[] = $currentChunk;
        $currentChunk = '';
    }

    $currentChunk .= ($currentChunk === '' ? '' : ' ') . $chunk;
}

if ($currentChunk !== '') {
    $translationChunks[] = $currentChunk;
}

// --------------------------------------------------
// Send each chunk to the Python translation API
// --------------------------------------------------

$translatedChunks = [];

foreach ($translationChunks as $chunk) {
    $payload = json_encode([
        'source_language' => $sourceLanguageCode,
        'target_language' => $targetLanguageCode,
        'text' => $chunk,
    ], JSON_UNESCAPED_UNICODE);

    if ($payload === false) {
        redirectWithError('Could not prepare the document for translation.');
    }

    $ch = curl_init('http://127.0.0.1:5000/api/translate');

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_TIMEOUT => 300,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);

    curl_close($ch);

    if ($response === false || $httpCode !== 200) {
        error_log('Translation API error: ' . $curlError);
        redirectWithError(
            'Translation failed. Make sure your Python translation API is running.'
        );
    }

    $result = json_decode($response, true);

    if (
        !is_array($result) ||
        !isset($result['translated_text']) ||
        trim((string) $result['translated_text']) === ''
    ) {
        redirectWithError('The translation API returned an invalid response.');
    }

    $translatedChunks[] = trim($result['translated_text']);
}

$translatedText = implode("\n\n", $translatedChunks);

// --------------------------------------------------
// Save the translated document
// --------------------------------------------------

$outputDirectory = __DIR__ . '/storage/translations';

if (
    !is_dir($outputDirectory) &&
    !mkdir($outputDirectory, 0755, true) &&
    !is_dir($outputDirectory)
) {
    redirectWithError('Could not create the translated-document storage folder.');
}

$fileId = bin2hex(random_bytes(16));
$outputPath = $outputDirectory . '/' . $fileId . '.txt';

if (file_put_contents($outputPath, $translatedText) === false) {
    redirectWithError('Could not save the translated document.');
}

// Store the ID, not a user-supplied filename, in the session.
$_SESSION['translated_document'] = $fileId;
$_SESSION['translated_document_name'] =
    pathinfo($originalName, PATHINFO_FILENAME) . '_' .
    $targetLanguage . '.txt';

$_SESSION['document_status'] = 'success';

header('Location: document_translate.php');
exit;
