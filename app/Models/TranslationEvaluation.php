<?php
namespace Napstar\MultilingualTranslationSystem\Models;

use Napstar\MultilingualTranslationSystem\Services\Database;
use PDO;
use InvalidArgumentException;

class TranslationEvaluation
{
    private PDO $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Save a user's corrected translation and similarity score.
     */
    
    public function create(
        int $userId,
        int $sourceLanguageId,
        int $targetLanguageId,
        string $sourceText,
        string $machineTranslation,
        string $correctedTranslation
    ): int {
        if (trim($sourceText) === '') {
            throw new InvalidArgumentException(
                'Source text cannot be empty.'
            );
        }

        if (trim($machineTranslation) === '') {
            throw new InvalidArgumentException(
                'Machine translation cannot be empty.'
            );
        }

        if (trim($correctedTranslation) === '') {
            throw new InvalidArgumentException(
                'Corrected translation cannot be empty.'
            );
        }

        $stmt = $this->db->prepare(
            'INSERT INTO translation_evaluations (
                user_id,
                source_language_id,
                target_language_id,
                source_text,
                machine_translation,
                corrected_translation
            ) VALUES (
                :user_id,
                :source_language_id,
                :target_language_id,
                :source_text,
                :machine_translation,
                :corrected_translation
            )'
        );

        $stmt->execute([
            'user_id' => $userId,
            'source_language_id' => $sourceLanguageId,
            'target_language_id' => $targetLanguageId,
            'source_text' => $sourceText,
            'machine_translation' => $machineTranslation,
            'corrected_translation' => $correctedTranslation
        ]);

        return (int) $this->db->lastInsertId();
    }


    /**
     * Retrieve evaluations belonging to one user.
     */
    public function getByUserId(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT
                e.id,
                e.source_text,
                e.machine_translation,
                e.corrected_translation,
                e.similarity_score,
                e.created_at,
                sl.name AS source_language,
                tl.name AS target_language
             FROM translation_evaluations e
             LEFT JOIN languages sl
                ON sl.id = e.source_language_id
             LEFT JOIN languages tl
                ON tl.id = e.target_language_id
             WHERE e.user_id = :user_id
             ORDER BY e.created_at DESC'
        );

        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
