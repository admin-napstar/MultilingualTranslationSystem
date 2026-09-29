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

    <div class="dashboard-overlay">

        <h2>
            Welcome,
            <?= htmlspecialchars($_SESSION['user_name']) ?> 
        </h2>

        <p>
            Welcome to the Multilingual Translation System.
        </p>

    </div>

</div>

<?php

require __DIR__ . '/views/layouts/footer.php';

?>