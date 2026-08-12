<?php

namespace Napstar\MultilingualTranslationSystem\Services;

use PDO;
use PDOException;

class Database
{
    private PDO $connection;

    public function __construct()
    {
        $host = 'localhost';
        $database = 'multilingual_translation';
        $username = 'root';
        $password = '';

        try {
            $this->connection = new PDO(
                "mysql:host={$host};dbname={$database};charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            throw new PDOException(
                'Database connection failed: ' . $e->getMessage()
            );
        }
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}