<?php

namespace Napstar\MultilingualTranslationSystem\Controllers;

use Napstar\MultilingualTranslationSystem\Models\Language;
use Napstar\MultilingualTranslationSystem\Models\Translation;
use Napstar\MultilingualTranslationSystem\Services\TranslationService;

class TranslationController
{
    private Language $languageModel;
    private Translation $translationModel;
    private TranslationService $translationService;

    public function __construct()
    {
        $this->languageModel = new Language();
        $this->translationModel = new Translation();
        $this->translationService = new TranslationService();
    }

    public function getLanguages(): array
    {
        return $this->languageModel->getActiveLanguages();
    }

    public function translate(
        int $userId,
        int $sourceLanguageId,
        int $targetLanguageId,
        string $sourceText
    ): string {

        $sourceLanguage =
            $this->languageModel->findById($sourceLanguageId);

        $targetLanguage =
            $this->languageModel->findById($targetLanguageId);

        if (!$sourceLanguage || !$targetLanguage) {
            throw new \Exception(
                'Invalid source or target language.'
            );
        }

        $sourceText = trim($sourceText);

        if ($sourceText === '') {
            throw new \Exception(
                'Please enter text to translate.'
            );
        }

        $translatedText =
            $this->translationService->translate(
                $sourceText,
                $sourceLanguage['code'],
                $targetLanguage['code']
            );

        $this->translationModel->create(
            $userId,
            $sourceLanguageId,
            $targetLanguageId,
            $sourceText,
            $translatedText
        );

        return $translatedText;
    }
}