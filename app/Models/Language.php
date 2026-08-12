<?php

namespace Napstar\MultilingualTranslationSystem\Models;

use Napstar\MultilingualTranslationSystem\Services\Database;
use PDO;

class Language
{
    private PDO $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getActiveLanguages(): array
    {
        $stmt = $this->db->query(
            'SELECT id, name, code
             FROM languages
             WHERE is_active = 1
             ORDER BY name'
        );

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, code
             FROM languages
             WHERE id = :id
             LIMIT 1'
        );

        $stmt->execute([
            'id' => $id
        ]);

        $language = $stmt->fetch();

        return $language ?: null;
    }
}