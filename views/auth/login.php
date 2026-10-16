<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Multilingual Translation System</title>

    <style>


<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    min-height: 100vh;
    padding: 24px;
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

.login-container {
    width: 100%;
    max-width: 440px;
}

/* Glossy glass card */
.login-card {
    position: relative;
    overflow: hidden;
    isolation: isolate;
    padding: 42px 35px;
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

/* Reflective shine across the card */
.login-card::before {
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

.login-card > * {
    position: relative;
}

h1 {
    margin: 0 0 12px;
    text-align: center;
    color: #172554;
    font-size: 30px;
    letter-spacing: -0.7px;
}

.subtitle {
    text-align: center;
    color: #64748b;
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 32px;
}

/* Form fields */
.form-group {
    margin-bottom: 23px;
}

label {
    display: block;
    margin-bottom: 9px;
    font-size: 14px;
    font-weight: 700;
    color: #1e293b;
}

input {
    display: block;
    width: 100%;
    padding: 14px;
    border: 1px solid #cbdaf2;
    border-radius: 11px;
    outline: none;

    background: linear-gradient(
        180deg, #ffffff, #f1f6ff
    );

    color: #1e293b;
    font-size: 15px;

    box-shadow:
        inset 0 2px 4px rgba(30, 64, 175, 0.04),
        0 3px 8px rgba(30, 64, 175, 0.05);

    transition: border-color 0.25s,
                box-shadow 0.25s;
}

input:focus {
    border-color: #3b82f6;
    background: #ffffff;

    box-shadow:
        0 0 0 4px rgba(59, 130, 246, 0.13),
        0 5px 15px rgba(37, 99, 235, 0.12);
}

/* Shiny blue login button */
button {
    position: relative;
    overflow: hidden;
    width: 100%;
    padding: 15px;
    margin-top: 3px;
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

/* Button's glossy top reflection */
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

/* PHP feedback messages */
.error {
    padding: 12px;
    margin-bottom: 20px;
    border-radius: 9px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #b91c1c;
    font-size: 14px;
}

.success {
    padding: 12px;
    margin-bottom: 20px;
    border-radius: 9px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #166534;
    font-size: 14px;
}

/* Registration link */
.register-link {
    text-align: center;
    margin-top: 25px;
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

/* Smaller screens */
@media (max-width: 480px) {
    .login-card {
        padding: 32px 23px;
        border-radius: 20px;
    }

    h1 {
        font-size: 26px;
    }
}
</style>







   
    </style>
</head>

























<body>

<div class="login-container">

    <div class="login-card">

        <h1>Welcome Back</h1>

        <p class="subtitle">
            Multilingual Translation System
        </p>

        <?php if (!empty($error)): ?>

            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>

        <?php if (!empty($success)): ?>

            <div class="success">
                <?= htmlspecialchars($success) ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="form-group">

                <label for="email">
                    Email Address
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                    value="<?= htmlspecialchars($email ?? '') ?>"
                >

            </div>

            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                >

            </div>

            <button type="submit">
                Login
            </button>

        </form>

        <div class="register-link">

            Don't have an account?
            <a href="register.php">Create Account</a>

        </div>

    </div>

</div>

</body>
</html>