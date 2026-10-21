
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

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Inter, "Segoe UI", Arial, sans-serif;
            background:
                radial-gradient(
                    circle at 15% 15%,
                    rgba(89, 139, 255, 0.25),
                    transparent 35%
                ),
                radial-gradient(
                    circle at 85% 80%,
                    rgba(159, 105, 255, 0.20),
                    transparent 35%
                ),
                linear-gradient(135deg, #0b1120, #172447 55%, #10182d);
            color: #f8fafc;
            overflow-x: hidden;
        }

        .verification-wrapper {
            width: 100%;
            max-width: 470px;
            position: relative;
        }

        .verification-glow {
            position: absolute;
            inset: -12px;
            border-radius: 36px;
            background: linear-gradient(
                135deg,
                rgba(96, 165, 250, 0.40),
                rgba(167, 139, 250, 0.20),
                rgba(45, 212, 191, 0.18)
            );
            filter: blur(28px);
            opacity: 0.65;
            pointer-events: none;
        }

        .verification-card {
            position: relative;
            overflow: hidden;
            padding: 42px 36px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 28px;
            background: linear-gradient(
                145deg,
                rgba(255, 255, 255, 0.13),
                rgba(255, 255, 255, 0.055)
            );
            box-shadow:
                0 28px 80px rgba(0, 0, 0, 0.35),
                inset 0 1px 1px rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            animation: bubbleAppear 0.65s ease both;
        }

        .verification-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 12%;
            width: 76%;
            height: 1px;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.8),
                transparent
            );
        }

        .verification-card::after {
            content: "";
            position: absolute;
            width: 190px;
            height: 190px;
            top: -125px;
            right: -85px;
            border-radius: 50%;
            background: rgba(147, 197, 253, 0.13);
            filter: blur(4px);
            pointer-events: none;
        }

        .verification-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 28px;
            color: #dbeafe;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1.8px;
            text-transform: uppercase;
        }

        .verification-brand-mark {
            width: 32px;
            height: 32px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.10);
            font-size: 17px;
        }

        .verification-icon {
            width: 82px;
            height: 82px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            border: 1px solid rgba(255, 255, 255, 0.24);
            border-radius: 26px;
            background: linear-gradient(
                145deg,
                rgba(96, 165, 250, 0.28),
                rgba(139, 92, 246, 0.18)
            );
            box-shadow:
                0 10px 35px rgba(59, 130, 246, 0.16),
                inset 0 1px rgba(255, 255, 255, 0.25);
            font-size: 36px;
        }

        .verification-card h1 {
            margin: 0 0 12px;
            text-align: center;
            font-size: clamp(25px, 5vw, 31px);
            font-weight: 750;
            letter-spacing: -0.8px;
            color: #ffffff;
        }

        .verification-description {
            max-width: 330px;
            margin: 0 auto 28px;
            color: #cbd5e1;
            font-size: 14px;
            line-height: 1.8;
            text-align: center;
        }

        .verification-label {
            display: block;
            margin-bottom: 10px;
            color: #e2e8f0;
            font-size: 13px;
            font-weight: 650;
        }

        .verification-input {
            display: block;
            width: 100%;
            min-height: 60px;
            padding: 14px;
            border: 1px solid rgba(255, 255, 255, 0.19);
            border-radius: 14px;
            outline: none;
            background: rgba(8, 15, 32, 0.42);
            color: #ffffff;
            font: inherit;
            font-size: 25px;
            font-weight: 700;
            letter-spacing: 10px;
            text-align: center;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .verification-input::placeholder {
            color: rgba(203, 213, 225, 0.4);
            letter-spacing: 8px;
        }

        .verification-input:focus {
            border-color: #93c5fd;
            box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.13);
        }

        .verification-error {
            margin: 0 0 18px;
            padding: 12px 14px;
            border: 1px solid rgba(252, 165, 165, 0.35);
            border-radius: 12px;
            background: rgba(127, 29, 29, 0.25);
            color: #fecaca;
            font-size: 13px;
            line-height: 1.6;
        }

        .verification-button {
            width: 100%;
            min-height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
            padding: 14px 18px;
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 14px;
            background: linear-gradient(110deg, #3b82f6, #6366f1);
            color: #ffffff;
            font: inherit;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 9px 24px rgba(59, 130, 246, 0.24);
            transition: transform 0.2s, filter 0.2s, box-shadow 0.2s;
        }

        .verification-button:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
            box-shadow: 0 13px 30px rgba(59, 130, 246, 0.32);
        }

        .verification-button:active {
            transform: translateY(0);
        }

        .verification-divider {
            height: 1px;
            margin: 26px 0 20px;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.17),
                transparent
            );
        }

        .verification-cancel {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            color: #cbd5e1;
            font-size: 13px;
            text-decoration: none;
            transition: color 0.2s;
        }

        .verification-cancel:hover {
            color: #ffffff;
        }

        .verification-footer {
            margin-top: 25px;
            color: #94a3b8;
            font-size: 11px;
            letter-spacing: 0.5px;
            text-align: center;
        }

        @keyframes bubbleAppear {
            from {
                opacity: 0;
                transform: translateY(18px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @media (max-width: 520px) {
            body {
                padding: 18px;
            }

            .verification-card {
                padding: 32px 22px;
                border-radius: 23px;
            }

            .verification-icon {
                width: 72px;
                height: 72px;
                border-radius: 22px;
            }

            .verification-input {
                letter-spacing: 7px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .verification-card {
                animation: none;
            }

            .verification-button {
                transition: none;
            }
        }
    </style>
</head>

<body>

    <main class="verification-wrapper">

        <div class="verification-glow" aria-hidden="true"></div>

        <section class="verification-card">

            <div class="verification-brand">
          
                <span>Multilingual Translation System</span>
            </div>

           

            <h1>Verify Your Identity</h1>

            <p class="verification-description">
                One more step to access your account.
                Open your authenticator app and enter your
                six-digit security code below.
            </p>

            <?php if ($error !== null): ?>
                <p class="verification-error" role="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endif; ?>

            <form method="POST" action="two-factor-verify.php">

                <label class="verification-label" for="code">
                    Authenticator code
                </label>

                <input
                    class="verification-input"
                    type="text"
                    id="code"
                    name="code"
                    inputmode="numeric"
                    pattern="[0-9]{6}"
                    minlength="6"
                    maxlength="6"
                    autocomplete="one-time-code"
                    placeholder="••••••"
                    aria-describedby="code-help"
                    required
                    autofocus
                >

                <p id="code-help" style="
                    margin: 10px 0 0;
                    color: #94a3b8;
                    font-size: 12px;
                    text-align: center;
                ">
                    Enter the current code from your authenticator app.
                </p>

                <button class="verification-button" type="submit">
                    Verify and Continue
                    <span aria-hidden="true">→</span>
                </button>

            </form>

            <div class="verification-divider"></div>

            <a class="verification-cancel" href="logout.php">
                <span aria-hidden="true">←</span>
                Cancel and log out
            </a>

        </section>

        <footer class="verification-footer">
            PROTECTED ACCESS · ACCOUNT SECURITY
        </footer>

    </main>

    <script>
        const codeInput = document.getElementById('code');

        codeInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 6);
        });
    </script>

</body>
</html>
