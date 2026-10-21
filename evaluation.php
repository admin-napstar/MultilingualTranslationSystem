
<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

use Napstar\MultilingualTranslationSystem\Services\Database;

$pageTitle = 'Translation Evaluation';
$translations = [];
$error = '';

try {
    $userId = filter_var(
        $_SESSION['user_id'],
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    if ($userId === false) {
        throw new RuntimeException('Invalid user session.');
    }

    $database = new Database();
    $db = $database->getConnection();

    $stmt = $db->prepare(
        'SELECT
            t.id,
            t.source_text,
            t.translated_text,
            t.source_language_id,
            t.target_language_id,
            sl.name AS source_language,
            sl.code AS source_language_code,
            tl.name AS target_language,
            tl.code AS target_language_code
        FROM translations t
        LEFT JOIN languages sl
            ON sl.id = t.source_language_id
        LEFT JOIN languages tl
            ON tl.id = t.target_language_id
        WHERE t.user_id = :user_id
        ORDER BY t.created_at DESC'
    );

    $stmt->execute(['user_id' => $userId]);
    $translations = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {
    error_log('Evaluation page error: ' . $e->getMessage());
    $error = 'Unable to load your translations. Please try again later.';
}

require_once __DIR__ . '/views/layouts/header.php';
?>

<main style="max-width: 1000px; margin: 30px auto; padding: 20px;">

    <h1>Translation Evaluation</h1>

    <p>
        Review your NLLB translations, check the model-confidence indicator,
        and correct translations when necessary.
    </p>

    <?php if ($error !== ''): ?>

        <div role="alert" style="padding: 12px; background: #fee2e2; color: #991b1b; border-radius: 8px;">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>

    <?php elseif (empty($translations)): ?>

        <p>You have no saved text translations to evaluate yet. Translate some text first.</p>

    <?php else: ?>

        <label for="translationId"><strong>Select a saved translation</strong></label>

        <select id="translationId" style="display:block; width:100%; padding:10px; margin:10px 0 20px;">
            <option value="">-- Choose a translation --</option>

            <?php foreach ($translations as $translation): ?>
                <option value="<?= (int) $translation['id'] ?>">
                    <?= htmlspecialchars(
                        mb_substr($translation['source_text'], 0, 70),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                    — <?= htmlspecialchars(
                        $translation['target_language'] ?? 'Unknown',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="sourceText"><strong>Original text</strong></label>
        <textarea id="sourceText" readonly rows="4"
            style="display:block; width:100%; box-sizing:border-box; padding:10px; margin:8px 0 20px;"></textarea>

        <label for="machineTranslation"><strong>NLLB translation</strong></label>
        <textarea id="machineTranslation" readonly rows="4"
            style="display:block; width:100%; box-sizing:border-box; padding:10px; margin:8px 0 20px;"></textarea>

        <section style="padding:16px; margin-bottom:20px; border-radius:10px; background:#f3f4f6;">
            <strong>Model-confidence indicator</strong>
            <h2 id="confidenceValue">Not calculated</h2>
            <p id="confidenceNote">
                Select a translation to calculate its confidence indicator.
            </p>
            <button type="button" id="checkConfidence">
                Check NLLB Confidence
            </button>
        </section>

        <label for="correctedTranslation"><strong>Your corrected translation</strong></label>
        <textarea id="correctedTranslation" rows="5"
            placeholder="Enter your corrected translation here..."
            style="display:block; width:100%; box-sizing:border-box; padding:10px; margin:8px 0 20px;"></textarea>

        <button type="button" id="saveEvaluation" disabled>
            Save Correction
        </button>

        <p id="evaluationMessage" role="status"></p>

        <script>
            const translations = <?= json_encode(
                $translations,
                JSON_HEX_TAG |
                JSON_HEX_APOS |
                JSON_HEX_AMP |
                JSON_HEX_QUOT
            ) ?>;

            const select = document.getElementById('translationId');
            const source = document.getElementById('sourceText');
            const machine = document.getElementById('machineTranslation');
            const corrected = document.getElementById('correctedTranslation');
            const confidenceValue = document.getElementById('confidenceValue');
            const confidenceNote = document.getElementById('confidenceNote');
            const checkButton = document.getElementById('checkConfidence');
            const saveButton = document.getElementById('saveEvaluation');
            const message = document.getElementById('evaluationMessage');

            let selectedTranslation = null;
            let confidenceChecked = false;

            select.addEventListener('change', function () {
                selectedTranslation = translations.find(
                    item => String(item.id) === this.value
                ) || null;

                source.value = selectedTranslation?.source_text ?? '';
                machine.value = selectedTranslation?.translated_text ?? '';
                corrected.value = '';

                confidenceValue.textContent = 'Not calculated';
                confidenceNote.textContent =
                    'Click the button to request a fresh confidence indicator from NLLB.';

                confidenceChecked = false;
                saveButton.disabled = true;
                message.textContent = '';
            });

            
            corrected.addEventListener('input', function () {
                saveButton.disabled = !(
                    selectedTranslation &&
                    confidenceChecked &&
                    this.value.trim() !== ''
                );

                if (selectedTranslation && this.value.trim() !== '') {
                    message.textContent = confidenceChecked
                        ? 'Correction entered. You can now save it.'
                        : 'Please check the NLLB confidence before saving.';
                } else {
                    message.textContent = '';
                }
            });


            checkButton.addEventListener('click', async function () {
                if (!selectedTranslation) {
                    message.textContent = 'Please select a translation first.';
                    return;
                }

                checkButton.disabled = true;
                confidenceValue.textContent = 'Checking...';
                message.textContent = '';

                try {
                    const response = await fetch(
                        'evaluate_translation.php',
                        {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                source_language: selectedTranslation.source_language_code,
                                target_language: selectedTranslation.target_language_code,
                                text: selectedTranslation.source_text
                            })
                        }
                    );

                    if (!response.ok) {
                        throw new Error('The translation API returned an error.');
                    }

                    const result = await response.json();

                    if (typeof result.confidence !== 'number') {
                        throw new Error('The API did not return a confidence value.');
                    }

                    machine.value = result.translated_text;
                    confidenceValue.textContent = result.confidence.toFixed(2) + '%';
                    confidenceNote.textContent = result.confidence_note;
                    confidenceChecked = true;

                    saveButton.disabled = !(
                        selectedTranslation &&
                        confidenceChecked &&
                        corrected.value.trim() !== ''
                    );
                    message.textContent = 'Confidence indicator loaded.';

                } catch (error) {
                    confidenceValue.textContent = 'Unavailable';
                    confidenceNote.textContent =
                        'Make sure the Python API is running and try again.';
                    message.textContent = error.message;

                } finally {
                    checkButton.disabled = false;
                }
            });

            
            
            saveButton.addEventListener('click', async function () {
                const translationId = select.value;
                const correctedTranslation = corrected.value.trim();

                if (!translationId) {
                    message.textContent = 'Please select a translation first.';
                    return;
                }

                if (!correctedTranslation) {
                    message.textContent = 'Please enter your corrected translation.';
                    return;
                }

                saveButton.disabled = true;
                saveButton.textContent = 'Saving...';
                message.textContent = '';

                try {
                    const response = await fetch('save_evaluation.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            translation_id: translationId,
                            corrected_translation: correctedTranslation
                        })
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        throw new Error(
                            result.error || 'Unable to save correction.'
                        );
                    }

                    message.textContent = result.message;

                    // Clear the correction after a successful save.
                    corrected.value = '';

                } catch (error) {
                    message.textContent = error.message;
                } finally {
                    saveButton.disabled = false;
                    saveButton.textContent = 'Save Correction';
                }
            });



        </script>

    <?php endif; ?>

</main>

<?php require_once __DIR__ . '/views/layouts/footer.php'; ?>
