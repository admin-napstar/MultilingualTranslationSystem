<?php

namespace Napstar\MultilingualTranslationSystem\Models;

use Napstar\MultilingualTranslationSystem\Services\Database;
use PDO;

class User
{
    private PDO $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM users WHERE email = :email LIMIT 1'
        );

        $stmt->execute([
            'email' => $email
        ]);

        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function create(
        string $name,
        string $email,
        string $password,
        string $role = 'user'
    ): int {
        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $stmt = $this->db->prepare(
            'INSERT INTO users
                (name, email, password, role)
             VALUES
                (:name, :email, :password, :role)'
        );

        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => $role
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM users WHERE id = :id LIMIT 1'
        );

        $stmt->execute([
            'id' => $id
        ]);

        $user = $stmt->fetch();

        return $user ?: null;
    }
	
	public function updateTwoFactorSettings(
		int $userId,
		?string $secret,
		bool $enabled
	): bool {
		$stmt = $this->db->prepare(
			'UPDATE users
			 SET two_factor_secret = :secret,
				 two_factor_enabled = :enabled
			 WHERE id = :id'
		);

		return $stmt->execute([
			'secret' => $secret,
			'enabled' => $enabled ? 1 : 0,
			'id' => $userId
		]);
	}

	public function getTwoFactorSettings(int $userId): ?array
	{
		$stmt = $this->db->prepare(
			'SELECT id, two_factor_enabled, two_factor_secret
			 FROM users
			 WHERE id = :id
			 LIMIT 1'
		);

		$stmt->execute([
			'id' => $userId
		]);

		$user = $stmt->fetch();

		return $user ?: null;
	}
}