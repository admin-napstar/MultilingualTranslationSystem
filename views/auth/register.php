
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Account - Multilingual Translation System</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            padding: 25px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;

            background:
                radial-gradient(ellipse at 15% 15%,
                    rgba(96, 165, 250, 0.5), transparent 40%),
                radial-gradient(ellipse at 85% 80%,
                    rgba(139, 92, 246, 0.35), transparent 40%),
                linear-gradient(135deg, #0f172a, #172554, #1e3a8a);
        }

        .register-container {
            width: 100%;
            max-width: 450px;
        }

        /* Glossy glass registration card */
        .register-card {
            position: relative;
            overflow: hidden;
            isolation: isolate;
            padding: 35px;
            border-radius: 24px;

            background: linear-gradient(
                145deg,
                rgba(255, 255, 255, 0.97),
                rgba(239, 246, 255, 0.90) 55%,
                rgba(219, 234, 254, 0.94)
            );

            border: 1px solid rgba(255, 255, 255, 0.9);

            box-shadow:
                0 30px 80px rgba(0, 0, 0, 0.35),
                0 0 35px rgba(96, 165, 250, 0.25),
                inset 0 2px 3px rgba(255, 255, 255, 1);
        }

        /* Reflective highlight */
        .register-card::before {
            content: "";
            position: absolute;
            top: -100px;
            left: -80px;
            width: 150%;
            height: 220px;
            z-index: -1;
            pointer-events: none;

            background: linear-gradient(
                115deg,
                transparent 20%,
                rgba(255, 255, 255, 0.8) 40%,
                rgba(255, 255, 255, 0.12) 55%,
                transparent 75%
            );

            transform: rotate(-8deg);
        }

        .register-card > * {
            position: relative;
        }

        h1 {
            margin: 0 0 10px;
            text-align: center;
            color: #172554;
            font-size: 28px;
            letter-spacing: -0.5px;
        }

        .subtitle {
            text-align: center;
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
        }

        input {
            display: block;
            width: 100%;
            padding: 13px;
            border: 1px solid #cbdaf2;
            border-radius: 10px;
            outline: none;

            background: linear-gradient(
                180deg, #ffffff, #f1f6ff
            );

            color: #1e293b;
            font-size: 15px;

            box-shadow:
                inset 0 2px 4px rgba(30, 64, 175, 0.04),
                0 3px 8px rgba(30, 64, 175, 0.05);

            transition: border-color 0.25s, box-shadow 0.25s;
        }

        input:focus {
            border-color: #3b82f6;
            background: #ffffff;

            box-shadow:
                0 0 0 4px rgba(59, 130, 246, 0.13),
                0 5px 15px rgba(37, 99, 235, 0.12);
        }

        /* Glossy create account button */
        button {
            position: relative;
            overflow: hidden;
            width: 100%;
            padding: 14px;
            margin-top: 4px;
            border: 1px solid rgba(255, 255, 255, 0.45);
            border-radius: 11px;

            background: linear-gradient(
                135deg, #60a5fa 0%, #3b82f6 45%, #1d4ed8 100%
            );

            color: white;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;

            box-shadow:
                0 8px 20px rgba(37, 99, 235, 0.32),
                inset 0 2px 2px rgba(255, 255, 255, 0.55),
                inset 0 -2px 3px rgba(30, 64, 175, 0.3);

            transition: transform 0.2s, box-shadow 0.2s;
        }

        button::before {
            content: "";
            position: absolute;
            top: 0;
            left: 8%;
            width: 84%;
            height: 45%;
            border-radius: 0 0 50% 50%;
            pointer-events: none;

            background: linear-gradient(
                180deg,
                rgba(255, 255, 255, 0.45),
                rgba(255, 255, 255, 0.03)
            );
        }

        button:hover {
            transform: translateY(-2px);

            box-shadow:
                0 12px 26px rgba(37, 99, 235, 0.42),
                inset 0 2px 2px rgba(255, 255, 255, 0.6);
        }

        button:active {
            transform: translateY(0);
        }

        /* PHP error message */
        .error {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 9px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            font-size: 14px;
            line-height: 1.5;
        }

        .login-link {
            text-align: center;
            margin-top: 22px;
            color: #64748b;
            font-size: 14px;
            line-height: 1.8;
        }

        a {
            color: #2563eb;
            font-weight: 700;
            text-decoration: none;
        }

        a:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            body {
                padding: 15px;
            }

            .register-card {
                padding: 28px 22px;
                border-radius: 20px;
            }

            h1 {
                font-size: 25px;
            }
        }
    </style>
</head>

<body>

<div class="register-container">

    <div class="register-card">

        <h1>Create Account</h1>

        <p class="subtitle">
            Multilingual Translation System
        </p>

        <?php if (!empty($error)): ?>

            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label for="name">Full Name</label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    required
                    autocomplete="name"
                    value="<?= htmlspecialchars($name ?? '') ?>"
                >
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                    autocomplete="email"
                    value="<?= htmlspecialchars($email ?? '') ?>"
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="new-password"
                >
            </div>

            <div class="form-group">
                <label for="confirm_password">
                    Confirm Password
                </label>

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    required
                    autocomplete="new-password"
                >
            </div>

            <button type="submit">
                Create Account
            </button>

        </form>

        <div class="login-link">
            Already have an account?
            <a href="login.php">Login</a>
        </div>

    </div>

</div>

</body>
</html>
