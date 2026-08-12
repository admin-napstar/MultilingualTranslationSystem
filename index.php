<?php

require_once __DIR__ . '/vendor/autoload.php';

use Napstar\MultilingualTranslationSystem\Controllers\AuthController;

try {

    $auth = new AuthController();

    $email = 'admin@example.com';

    $existingUser = $auth
        ->login($email, 'Admin@123');

    if ($existingUser) {

        echo '<h1>Login successful</h1>';

        echo '<p>Welcome, '
            . htmlspecialchars($existingUser['name'])
            . '</p>';

        echo '<p>Role: '
            . htmlspecialchars($existingUser['role'])
            . '</p>';

    } else {

        echo '<p>User does not exist yet.</p>';
    }

} catch (Exception $e) {

    echo '<h1>Application Error</h1>';

    echo '<p>'
        . htmlspecialchars($e->getMessage())
        . '</p>';
}