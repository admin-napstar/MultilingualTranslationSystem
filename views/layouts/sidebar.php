
<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Mobile menu button -->
<button
    type="button"
    class="mobile-menu-toggle"
    id="sidebarToggle"
    aria-label="Open navigation menu"
    aria-controls="appSidebar"
    aria-expanded="false"
>
    <i class="fas fa-bars" aria-hidden="true"></i>
</button>

<!-- Mobile backdrop -->
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<aside class="sidebar" id="appSidebar">
    <div class="logo">
        <h2>Lingua-SA</h2>
        <span>
            A Multilingual Translation System for South African Languages
        </span>
    </div>

    <nav aria-label="Main navigation">
        <p class="sidebar-heading">WORKSPACE</p>

        <a href="dashboard.php"
           class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
            <i class="fas fa-chart-line" aria-hidden="true"></i>
            <span>Dashboard</span>
        </a>

        <a href="translate.php"
           class="<?= $currentPage === 'translate.php' ? 'active' : '' ?>">
            <i class="fas fa-language" aria-hidden="true"></i>
            <span>Text Translation</span>
        </a>

        <a href="document_translate.php"
           class="<?= $currentPage === 'document_translate.php' ? 'active' : '' ?>">
            <i class="fas fa-file-alt" aria-hidden="true"></i>
            <span>Document Translation</span>
        </a>

        <a href="history.php"
           class="<?= $currentPage === 'history.php' ? 'active' : '' ?>">
            <i class="fas fa-history" aria-hidden="true"></i>
            <span>Translation History</span>
        </a>

        <p class="sidebar-heading management-heading">
            QUALITY &amp; MANAGEMENT
        </p>

        <a href="evaluation.php"
           class="<?= $currentPage === 'evaluation.php' ? 'active' : '' ?>">
            <i class="fas fa-chart-bar" aria-hidden="true"></i>
            <span>Translation Evaluation</span>
        </a>

        

        <a href="settings.php"
           class="<?= $currentPage === 'settings.php' ? 'active' : '' ?>">
            <i class="fas fa-cog" aria-hidden="true"></i>
            <span>Settings</span>
        </a>
    </nav>

    <div class="sidebar-bottom">
        <a href="logout.php">
            <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>