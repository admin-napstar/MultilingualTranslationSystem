<?php

namespace Napstar\MultilingualTranslationSystem\Controllers;

use Napstar\MultilingualTranslationSystem\Models\User;

class RegistrationController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function register(
        string $name,
        string $email,
        string $password,
        string $confirmPassword
    ): array {

        $name = trim($name);
        $email = trim($email);

        if ($name === '') {
            return [
                'success' => false,
                'message' => 'Please enter your name.'
            ];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Please enter a valid email address.'
            ];
        }

        if (strlen($password) < 8) {
            return [
                'success' => false,
                'message' => 'Password must contain at least 8 characters.'
            ];
        }

        if ($password !== $confirmPassword) {
            return [
                'success' => false,
                'message' => 'Passwords do not match.'
            ];
        }

        if ($this->userModel->findByEmail($email) !== null) {
            return [
                'success' => false,
                'message' => 'An account with this email already exists.'
            ];
        }

        $userId = $this->userModel->create(
            $name,
            $email,
            $password,
            'user'
        );

        return [
            'success' => true,
            'user_id' => $userId
        ];
    }
}