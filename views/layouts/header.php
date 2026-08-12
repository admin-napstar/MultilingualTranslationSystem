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

        <header class="topbar">

            <div>
                <h1>
                    <?= htmlspecialchars($pageTitle) ?>
                </h1>
            </div>

            <div class="user-info">

                <span>
                    <?= htmlspecialchars($_SESSION['user_name'] ?? 'Guest') ?>
                </span>

                <a href="logout.php">
                    Logout
                </a>

            </div>

        </header>

        <section class="content">