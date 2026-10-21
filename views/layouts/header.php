<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = $pageTitle ?? 'Multilingual Translation System';

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= htmlspecialchars($pageTitle) ?>
        - Multilingual Translation System
    </title>

    <link
        rel="stylesheet"
        href="public/css/app.css"
    >

</head>

<body>

<div class="app">

    <?php require __DIR__ . '/sidebar.php'; ?>

    <main class="main-content">
    
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');

    if (!toggle || !sidebar) return;

    toggle.addEventListener('click', function () {
        const isOpen = sidebar.classList.toggle('is-open');

        toggle.setAttribute('aria-expanded', String(isOpen));
        toggle.setAttribute(
            'aria-label',
            isOpen ? 'Close navigation' : 'Open navigation'
        );

        toggle.innerHTML = isOpen
            ? '<i class="fas fa-times"></i>'
            : '<i class="fas fa-bars"></i>';
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            sidebar.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-label', 'Open navigation');
            toggle.innerHTML = '<i class="fas fa-bars"></i>';
        }
    });
});
</script>


<header class="topbar">
    <div class="topbar-title">
        <button
            class="sidebar-toggle"
            id="sidebarToggle"
            type="button"
            aria-label="Open navigation"
            aria-expanded="false"
            aria-controls="sidebar"
        >
            <i class="fas fa-bars"></i>
        </button>

        <h1><?= htmlspecialchars($pageTitle) ?></h1>
    </div>

            <div class="user-info">

                <span>
                    <?= htmlspecialchars($_SESSION['user_name'] ?? 'Guest') ?>
                </span>

                

            </div>

        </header>

        <section class="content">