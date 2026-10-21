
<?php

session_start();

use Napstar\MultilingualTranslationSystem\Services\Database;
use PhpOffice\PhpWord\IOFactory;
use Smalot\PdfParser\Parser;

require_once __DIR__ . '/vendor/autoload.php';

function redirectWithError(string $message): void
{
    $_SESSION['document_status'] = $message;
    header('Location: document_translate.php');
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
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

if (!is_uploaded_file($file['tmp_name'])) {
    redirectWithError('The uploaded document could not be verified.');
}

$userId = filter_var(
    $_SESSION['user_id'],
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if ($userId === false) {
    redirectWithError('Your session is invalid. Please log in again.');
}

$targetLanguage = trim($_POST['target_language'] ?? '');

if ($targetLanguage === '') {
    redirectWithError('Please select a target language.');
}

// --------------------------------------------------
// Connect to the database and find real language IDs
// --------------------------------------------------

try {
    $database = new Database();
    $db = $database->getConnection();

    // English is the fixed source language.
    $stmt = $db->prepare(
        'SELECT id, name, code
         FROM languages
         WHERE code = :code AND is_active = 1
         LIMIT 1'
    );

    $stmt->execute(['code' => 'eng_Latn']);
    $sourceLanguage = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$sourceLanguage) {
        redirectWithError(
            'English is not configured as an active language in the database.'
        );
    }

    // The submitted target must match an active database language.
    $stmt = $db->prepare(
        'SELECT id, name, code
         FROM languages
         WHERE name = :name AND is_active = 1
         LIMIT 1'
    );

    $stmt->execute(['name' => $targetLanguage]);
    $targetLanguageRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$targetLanguageRow) {
        redirectWithError(
            'The selected language is not active in the database. ' .
            'Please choose a supported language.'
        );
    }

} catch (Throwable $e) {
    error_log('Document database setup error: ' . $e->getMessage());
    redirectWithError('Could not connect to the translation database.');
}

$sourceLanguageId = (int) $sourceLanguage['id'];
$sourceLanguageCode = $sourceLanguage['code'];

$targetLanguageId = (int) $targetLanguageRow['id'];
$targetLanguageCode = $targetLanguageRow['code'];
$targetLanguageName = $targetLanguageRow['name'];

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

    redirectWithError(
        'Could not read the document. Check that the file is valid.'
    );
}

$text = trim($text);

if ($text === '') {
    redirectWithError(
        'No readable text was found. Scanned PDFs and image-only ' .
        'documents are not supported yet.'
    );
}

// --------------------------------------------------
// Split the text into manageable chunks
// --------------------------------------------------

$chunks = preg_split(
    '/(?<=[.!?])\s+|\R+/u',
    $text,
    -1,
    PREG_SPLIT_NO_EMPTY
);

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

    while (mb_strlen($chunk, 'UTF-8') > 1000) {
        if ($currentChunk !== '') {
            $translationChunks[] = $currentChunk;
            $currentChunk = '';
        }

        $translationChunks[] = mb_substr(
            $chunk,
            0,
            1000,
            'UTF-8'
        );

        $chunk = mb_substr($chunk, 1000, null, 'UTF-8');
    }

    if (
        $currentChunk !== '' &&
        mb_strlen($currentChunk . ' ' . $chunk, 'UTF-8') > 1000
    ) {
        $translationChunks[] = $currentChunk;
        $currentChunk = '';
    }

    $currentChunk .= ($currentChunk === '' ? '' : ' ') . $chunk;
}

if ($currentChunk !== '') {
    $translationChunks[] = $currentChunk;
}

if (empty($translationChunks)) {
    redirectWithError('No translatable text was found.');
}

// --------------------------------------------------
// Translate each chunk using the existing Flask API
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
        !is_string($result['translated_text']) ||
        trim($result['translated_text']) === ''
    ) {
        redirectWithError(
            'The translation API returned an invalid response.'
        );
    }

    $translatedChunks[] = trim($result['translated_text']);
}

$translatedText = implode("\n\n", $translatedChunks);

// --------------------------------------------------
// Save both files and create the database record
// --------------------------------------------------

$storageDirectory = __DIR__ . '/storage';
$uploadDirectory = $storageDirectory . '/uploads';
$outputDirectory = $storageDirectory . '/translations';

foreach ([$uploadDirectory, $outputDirectory] as $directory) {
    if (
        !is_dir($directory) &&
        !mkdir($directory, 0755, true) &&
        !is_dir($directory)
    ) {
        redirectWithError(
            'Could not create the document storage folders.'
        );
    }
}

// A random ID prevents filenames from being used as storage paths.
try {
    $fileId = bin2hex(random_bytes(16));
} catch (Throwable $e) {
    redirectWithError('Could not prepare the document for saving.');
}

$uploadRelativePath = 'storage/uploads/' . $fileId . '.' . $extension;
$outputRelativePath = 'storage/translations/' . $fileId . '.txt';

$uploadPath = __DIR__ . '/' . $uploadRelativePath;
$outputPath = __DIR__ . '/' . $outputRelativePath;

$baseName = pathinfo($originalName, PATHINFO_FILENAME);
$translatedFilename = $baseName . '_' . $targetLanguageName . '.txt';

// Keep filenames within the database's varchar(255) limit.
if (mb_strlen($translatedFilename, 'UTF-8') > 255) {
    $translatedFilename = mb_substr(
        $translatedFilename,
        0,
        251,
        'UTF-8'
    ) . '.txt';
}

$uploadSaved = false;
$outputSaved = false;

try {
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new RuntimeException('Could not save the original document.');
    }

    $uploadSaved = true;

    if (file_put_contents($outputPath, $translatedText, LOCK_EX) === false) {
        throw new RuntimeException('Could not save the translated document.');
    }

    $outputSaved = true;

    // Store a completed record only after both files exist.
    $stmt = $db->prepare(
        'INSERT INTO documents
        (
            user_id,
            source_language_id,
            target_language_id,
            original_filename,
            translated_filename,
            file_path,
            translated_file_path,
            status
        )
        VALUES
        (
            :user_id,
            :source_language_id,
            :target_language_id,
            :original_filename,
            :translated_filename,
            :file_path,
            :translated_file_path,
            :status
        )'
    );

    $stmt->execute([
        'user_id' => $userId,
        'source_language_id' => $sourceLanguageId,
        'target_language_id' => $targetLanguageId,
        'original_filename' => mb_substr($originalName, 0, 255, 'UTF-8'),
        'translated_filename' => $translatedFilename,
        'file_path' => $uploadRelativePath,
        'translated_file_path' => $outputRelativePath,
        'status' => 'completed',
    ]);

} catch (Throwable $e) {
    error_log('Saving document translation failed: ' . $e->getMessage());

    // Avoid leaving orphaned files when saving fails.
    if ($uploadSaved && is_file($uploadPath)) {
        unlink($uploadPath);
    }

    if ($outputSaved && is_file($outputPath)) {
        unlink($outputPath);
    }

    redirectWithError(
        'The translation was created, but it could not be saved to your history.'
    );
}

// --------------------------------------------------
// Prepare the download page
// --------------------------------------------------

$_SESSION['translated_document'] = $fileId;
$_SESSION['translated_document_name'] = $translatedFilename;
$_SESSION['document_status'] = 'success';

header('Location: document_translate.php');
exit;
