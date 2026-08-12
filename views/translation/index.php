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

                <label for="source_language">
                    Source Language
                </label>

                <select
                    id="source_language"
                    name="source_language"
                    required
                >

                    <option value="">
                        Select source language
                    </option>

                    <?php foreach ($languages as $language): ?>

                        <option value="<?= $language['id'] ?>">

                            <?= htmlspecialchars($language['name']) ?>

                        </option>

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

                <textarea
                    id="source_text"
                    name="source_text"
                    rows="10"
                    placeholder="Enter text here..."
                    required
                ></textarea>

            </div>


            <div class="text-box">

                <label for="translated_text">
                    Translation
                </label>

                <textarea
                    id="translated_text"
                    rows="10"
                    readonly
                    placeholder="Your translation will appear here..."
                ><?= htmlspecialchars($translatedText ?? '') ?></textarea>

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