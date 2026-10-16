<?php

session_start();

require_once __DIR__ . '/vendor/autoload.php';

use Napstar\MultilingualTranslationSystem\Controllers\AuthController;

$error = null;
$success = null;
$email = '';

if (isset($_GET['registered'])) {
    $success = 'Account created successfully. Please log in.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {

        $error = 'Please enter your email and password.';

    } else {

        try {

            $auth = new AuthController();

            $user = $auth->login(
                $email,
                $password
            );

            
			
			if ($user !== null) {

				$twoFactor = $auth->getTwoFactorSettings(
					(int) $user['id']
				);

				if (
					$twoFactor !== null
					&& (int) $twoFactor['two_factor_enabled'] === 1
				) {
					// Keep the user pending until their code is verified.
					session_regenerate_id(true);

					$_SESSION['pending_2fa_user_id'] = (int) $user['id'];
					$_SESSION['pending_2fa_user_name'] = $user['name'];
					$_SESSION['pending_2fa_user_email'] = $user['email'];
					$_SESSION['pending_2fa_user_role'] = $user['role'];

					header('Location: two-factor-verify.php');
					exit;
				}

				// Normal login for users who have not enabled 2FA.
				session_regenerate_id(true);

				$_SESSION['user_id'] = $user['id'];
				$_SESSION['user_name'] = $user['name'];
				$_SESSION['user_email'] = $user['email'];
				$_SESSION['user_role'] = $user['role'];

				header('Location: dashboard.php');
				exit;
			}
            $error = 'Invalid email or password.';

        } catch (Exception $e) {

            $error = 'An error occurred while logging in.';
        }
    }
}

require __DIR__ . '/views/auth/login.php';