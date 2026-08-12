<?php

namespace Napstar\MultilingualTranslationSystem\Models;

use Napstar\MultilingualTranslationSystem\Services\Database;
use PDO;

class Translation
{
    private PDO $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function create(
        int $userId,
        int $sourceLanguageId,
        int $targetLanguageId,
        string $sourceText,
        string $translatedText
    ): int {

        $stmt = $this->db->prepare(
            'INSERT INTO translations
            (
                user_id,
                source_language_id,
                target_language_id,
                source_text,
                translated_text
            )
            VALUES
            (
                :user_id,
                :source_language_id,
                :target_language_id,
                :source_text,
                :translated_text
            )'
        );

        $stmt->execute([
            'user_id' => $userId,
            'source_language_id' => $sourceLanguageId,
            'target_language_id' => $targetLanguageId,
            'source_text' => $sourceText,
            'translated_text' => $translatedText
        ]);

        return (int) $this->db->lastInsertId();
    }
}