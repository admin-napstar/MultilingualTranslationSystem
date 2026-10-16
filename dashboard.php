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
    <h2>
        Welcome,
        <?= htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') ?>
    </h2>
    <div class="dashboard-overlay">
        <h2>
            Welcome,
            <?= htmlspecialchars($_SESSION['user_name']) ?> 
        </h2>

        <p>
            Welcome to the Multilingual Translation System.
        </p>

    </div>

    <hr>

    <h3>Account Security</h3>

    <p>
        Protect your account with two-factor authentication.
    </p>

    <p>
        <a href="two-factor-setup.php">
            Set Up Two-Factor Authentication
        </a>
    </p>

</div>


<?php

require __DIR__ . '/views/layouts/footer.php';

?>