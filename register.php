<?php

require_once __DIR__ . '/vendor/autoload.php';

use Napstar\MultilingualTranslationSystem\Controllers\RegistrationController;

$error = null;
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    try {

        $controller = new RegistrationController();

        $result = $controller->register(
            $name,
            $email,
            $password,
            $confirmPassword
        );

        if ($result['success']) {

            header('Location: login.php?registered=1');
            exit;
        }

        $error = $result['message'];

    } catch (Exception $e) {

        $error = $e->getMessage();
    }
}

require __DIR__ . '/views/auth/register.php';