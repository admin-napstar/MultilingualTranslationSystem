
<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Dashboard';

require __DIR__ . '/views/layouts/header.php';

?>

<div class="dashboard">

    <section class="dashboard-hero">
        <div class="hero-shine"></div>

        <div class="hero-content">
           

            <h2>
                Welcome back,
                <?= htmlspecialchars(
                    $_SESSION['user_name'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>!
            </h2>

            <p>
                Welcome to your Multilingual Translation System.
                Translate and manage content across languages
                in one place.
            </p>

        
        </div>

        <div class="hero-decoration" aria-hidden="true">
            <div class="glass-orb orb-one"></div>
            <div class="glass-orb orb-two"></div>
            <div class="glass-orb orb-three"></div>
        </div>
    </section>

    <section class="dashboard-section">
     

        <div class="security-card">
            <div class="security-icon" aria-hidden="true">
                <svg
                    width="30"
                    height="30"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <rect
                        x="4" y="10"
                        width="16" height="11"
                        rx="2"
                    />
                    <path d="M8 10V7a4 4 0 0 1 8 0v3" />
                    <path d="M12 14v3" />
                </svg>
            </div>

            <div class="security-info">
                <h4>Two-Factor Authentication</h4>
                <p>
                    Add an extra layer of protection
                    when accessing your account.
                </p>
            </div>

            <a class="security-button" href="two-factor-setup.php">
                Manage Security
                <span aria-hidden="true">→</span>
            </a>
        </div>
    </section>

</div>

<?php

require __DIR__ . '/views/layouts/footer.php';

?>
