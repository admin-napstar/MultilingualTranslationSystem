<div class="translation-page">

    <div class="translation-header">

        <h2>Text Translation</h2>

        <p>
            Translate text between supported South African languages.
        </p>

    </div>

    <form method="POST" action="translate.php">

        <div class="language-selection">

            <div class="form-group">
                <label for="source_language">Source Language</label>

                <select id="source_language" name="source_language" required>
                    <?php foreach ($languages as $language): ?>
                        <?php if ($language['code'] === 'eng_Latn'): ?>
                            <option value="<?= $language['id'] ?>" selected>
                                <?= htmlspecialchars($language['name']) ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>


            <div class="swap-language">
                ⇄
            </div>


            <div class="form-group">

                <label for="target_language">
                    Target Language
                </label>

                <select
                    id="target_language"
                    name="target_language"
                    required
                >

                    <option value="">
                        Select target language
                    </option>

                    <?php foreach ($languages as $language): ?>

                        <option value="<?= $language['id'] ?>">

                            <?= htmlspecialchars($language['name']) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

        </div>


        <div class="translation-boxes">

            <div class="text-box">

				<label for="source_text">
					Text to translate
				</label>

				<div class="textarea-wrapper">

					<textarea
						id="source_text"
						name="source_text"
						rows="10"
						placeholder="Enter text here..."
						required
					></textarea>

					<button
						type="button"
						id="mic-button"
						class="mic-button"
						title="Speak"
					>
						🎤
					</button>

				</div>

				<div id="speech-status" class="speech-status"></div>

			</div>


            <div class="text-box">
                <label for="translated_text">Translation</label>

                <div class="textarea-wrapper">
                    <textarea id="translated_text" rows="10" readonly
                        placeholder="Your translation will appear here..."><?= htmlspecialchars($translatedText ?? '') ?></textarea>

                    <button type="button" id="speaker-button"
                            class="speaker-button"
                            title="Listen to translation">
                        🔊
                    </button>
                </div>

                <div id="speech-output-status" class="speech-status"></div>
            </div>

        </div>


        <?php if (!empty($error)): ?>

            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>


        <button
            type="submit"
            class="translate-button"
        >
            Translate
        </button>

    </form>

</div>
<script>

document.addEventListener('DOMContentLoaded', function () {

    const micButton = document.getElementById('mic-button');
    const sourceText = document.getElementById('source_text');
    const sourceLanguage = document.getElementById('source_language');
    const speechStatus = document.getElementById('speech-status');
    const speakerButton = document.getElementById('speaker-button');
    const translatedText = document.getElementById('translated_text');
    const targetLanguage = document.getElementById('target_language');
    const speechOutputStatus = document.getElementById('speech-output-status');

    const SpeechRecognition =
        window.SpeechRecognition ||
        window.webkitSpeechRecognition;

    if (!SpeechRecognition) {

        micButton.disabled = true;

        speechStatus.textContent =
            'Speech recognition is not supported by this browser.';

        return;
    }

    const recognition = new SpeechRecognition();

    recognition.continuous = false;
    recognition.interimResults = false;

    function getSpeechLanguage() {

        const selectedOption =
            sourceLanguage.options[sourceLanguage.selectedIndex];

        if (!selectedOption || !selectedOption.value) {
            return null;
        }

        const languageName =
            selectedOption.textContent.trim();

        const languageMap = {

            'English': 'en-ZA',
            'Sesotho': 'st-ZA',
            'isiZulu': 'zu-ZA',
            'isiXhosa': 'xh-ZA',
            'Afrikaans': 'af-ZA',
            'Sepedi': 'nso-ZA',
            'Siswati': 'ss-ZA',
            'Tshivenda': 've-ZA',
            'itsonga': 'ts-ZA',
            'isiNdebele': 'nr-ZA'

        };

        return languageMap[languageName] || null;
    }

    micButton.addEventListener('click', function () {

        const language = getSpeechLanguage();

        if (!language) {

            speechStatus.textContent =
                'Please select a source language first.';

            return;
        }

        recognition.lang = language;

        speechStatus.textContent =
            '🎤 Listening... Speak now';

        micButton.disabled = true;

        try {

            recognition.start();

        } catch (error) {

            console.error(error);

            micButton.disabled = false;

        }
    });

    recognition.onresult = function (event) {

        const transcript =
            event.results[0][0].transcript;

        sourceText.value = transcript;

        speechStatus.textContent =
            'Speech converted to text successfully.';

    };

    recognition.onerror = function (event) {

        console.error(
            'Speech recognition error:',
            event.error
        );

        if (event.error === 'not-allowed') {

            speechStatus.textContent =
                'Microphone permission was denied.';

        } else if (event.error === 'no-speech') {

            speechStatus.textContent =
                'No speech was detected. Please try again.';

        } else {

            speechStatus.textContent =
                'Speech recognition failed. Please try again.';

        }
    };

    recognition.onend = function () {

        micButton.disabled = false;

        if (
            speechStatus.textContent ===
            '🎤 Listening... Speak now'
        ) {
            speechStatus.textContent = '';
        }
    };

    function getSpeechLanguage() {

        const selectedOption =
            targetLanguage.options[targetLanguage.selectedIndex];

        if (!selectedOption || !selectedOption.value) {
            return null;
        }

        const languageName =
            selectedOption.textContent.trim();

        const languageMap = {
            'English': 'en-ZA',
            'Sesotho': 'st-ZA',
            'isiZulu': 'zu-ZA',
            'isiXhosa': 'xh-ZA',
            'Afrikaans': 'af-ZA',
            'Sepedi': 'nso-ZA',
            'Siswati': 'ss-ZA',
            'Tshivenda': 've-ZA',
            'itsonga': 'ts-ZA',
            'isitsonga': 'ts-ZA',
            'isiNdebele': 'nr-ZA'
        };

        return languageMap[languageName] || null;
    }


    speakerButton.addEventListener('click', function () {

        const text = translatedText.value.trim();

        if (!text) {
            speechOutputStatus.textContent =
                'There is no translation to read.';
            return;
        }

        if (!('speechSynthesis' in window)) {
            speechOutputStatus.textContent =
                'Text-to-speech is not supported by this browser.';
            return;
        }

        // Stop anything currently being spoken
        window.speechSynthesis.cancel();

        const language = getSpeechLanguage();

        if (!language) {
            speechOutputStatus.textContent =
                'Please select a target language first.';
            return;
        }

        const utterance = new SpeechSynthesisUtterance(text);

        utterance.lang = language;
        utterance.rate = 0.9;
        utterance.pitch = 1;

        utterance.onstart = function () {
            speakerButton.textContent = '🔇';
            speechOutputStatus.textContent =
                '🔊 Reading translation...';
        };

        utterance.onend = function () {
            speakerButton.textContent = '🔊';
            speechOutputStatus.textContent = '';
        };

        utterance.onerror = function (event) {
            console.error('Speech synthesis error:', event);

            speakerButton.textContent = '🔊';

            speechOutputStatus.textContent =
                'Unable to read the translation.';
        };

        window.speechSynthesis.speak(utterance);
    });

});


</script>