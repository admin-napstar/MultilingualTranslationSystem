
<?php

session_start();

require_once __DIR__ . '/vendor/autoload.php';

use Napstar\MultilingualTranslationSystem\Controllers\AuthController;
use Napstar\MultilingualTranslationSystem\Services\TwoFactorAuth;

if (!isset($_SESSION['pending_2fa_user_id'])) {
    header('Location: login.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');

    if (!preg_match('/^\d{6}$/', $code)) {
        $error = 'Enter a valid six-digit authenticator code.';
    } else {
        try {
            $auth = new AuthController();

            $settings = $auth->getTwoFactorSettings(
                (int) $_SESSION['pending_2fa_user_id']
            );

            $twoFactor = new TwoFactorAuth();

            if (
                $settings !== null
                && (int) $settings['two_factor_enabled'] === 1
                && !empty($settings['two_factor_secret'])
                && $twoFactor->verifyCode(
                    $settings['two_factor_secret'],
                    $code
                )
            ) {
                session_regenerate_id(true);

                $_SESSION['user_id'] =
                    $_SESSION['pending_2fa_user_id'];

                $_SESSION['user_name'] =
                    $_SESSION['pending_2fa_user_name'];

                $_SESSION['user_email'] =
                    $_SESSION['pending_2fa_user_email'];

                $_SESSION['user_role'] =
                    $_SESSION['pending_2fa_user_role'];

                unset(
                    $_SESSION['pending_2fa_user_id'],
                    $_SESSION['pending_2fa_user_name'],
                    $_SESSION['pending_2fa_user_email'],
                    $_SESSION['pending_2fa_user_role']
                );

                header('Location: dashboard.php');
                exit;
            }

            $error = 'Invalid authenticator code. Please try again.';

        } catch (\Throwable $e) {
            $error = 'Unable to verify your code. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Verification</title>
    <link rel="stylesheet" href="public/css/app.css">
</head>
<body>
    <main class="container">
        <h1>Two-Factor Authentication</h1>

        <p>Open your authenticator app and enter the six-digit code.</p>

        <?php if ($error !== null): ?>
            <p class="error">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="two-factor-verify.php">
            <label for="code">Authenticator code</label>

            <input
                type="text"
                id="code"
                name="code"
                inputmode="numeric"
                pattern="[0-9]{6}"
                maxlength="6"
                autocomplete="one-time-code"
                required
            >

            <button type="submit">Verify and Continue</button>
        </form>

        <p><a href="logout.php">Cancel and log out</a></p>
    </main>
</body>
</html>
