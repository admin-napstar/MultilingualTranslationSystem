<?php

namespace Napstar\MultilingualTranslationSystem\Controllers;

use Napstar\MultilingualTranslationSystem\Models\User;

class AuthController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function register(
        string $name,
        string $email,
        string $password
    ): int {
        $existingUser = $this->userModel->findByEmail($email);

        if ($existingUser !== null) {
            throw new \Exception(
                'An account with this email already exists.'
            );
        }

        return $this->userModel->create(
            $name,
            $email,
            $password
        );
    }

    public function login(
        string $email,
        string $password
    ): ?array {
        $user = $this->userModel->findByEmail($email);

        if ($user === null) {
            return null;
        }

        if (!password_verify($password, $user['password'])) {
            return null;
        }

        return $user;
    }
}