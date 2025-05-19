<?php
session_start();
require_once 'includes/db.php';

$loggedIn = isset($_SESSION['user_id']);
$username = $loggedIn ? $_SESSION['username'] : '';
$role = $loggedIn ? $_SESSION['role'] : '';
?>

<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <title>Photo Gallery</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>Photo Gallery</h1>
    <nav>
        <ul>
            <?php if ($loggedIn): ?>
                <li><span>მოგესალმები, <?= htmlspecialchars($username) ?> (<?= htmlspecialchars($role) ?>)</span></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="auth/logout.php">Log out</a></li>
            <?php else: ?>
                <li><a href="auth/register.php">Register</a></li>
                <li><a href="auth/login.php">Log in</a></li>
                <li><a href="auth/contact.php">Contact</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
<div class="gallery-grid">
    <!-- ქარდი 1 -->
    <div class="card">
        <img src="images/Photo1.avif" alt="Mountains">
        <div class="card-content">
            <h3>Mountains</h3>
            <p>Beautiful mountain range photo</p>
        </div>
    </div>

    <!-- ქარდი 2 -->
    <div class="card">
        <img src="images/Photo2.jpeg" alt="City Lights">
        <div class="card-content">
            <h3>City Lights</h3>
            <p>Night view of a bustling city</p>
        </div>
    </div>

    <!-- ქარდი 3 -->
    <div class="card">
        <img src="images/Photo3.jpg" alt="Desert">
        <div class="card-content">
            <h3>Desert</h3>
            <p>Sandy dunes and desert vibes</p>
        </div>
    </div>

    <!-- ქარდი 4 -->
    <div class="card">
        <img src="images/Photo4.jpg" alt="Ocean">
        <div class="card-content">
            <h3>Ocean</h3>
            <p>Deep blue ocean view</p>
        </div>
    </div>

    <!-- ქარდი 5 -->
    <div class="card">
        <img src="images/Photo5.jpeg" alt="Nature">
        <div class="card-content">
            <h3>Nature</h3>
            <p>Green forest and wildlife</p>
        </div>
    </div>

    <!-- ქარდი 6 -->
    <div class="card">
        <img src="images/Photo6.jpg" alt="Sunset">
        <div class="card-content">
            <h3>Sunset</h3>
            <p>Colorful sky at sunset</p>
        </div>
    </div>

    <!-- ქარდი 7 -->
    <div class="card">
        <img src="images/Photo7.jpeg" alt="Travel">
        <div class="card-content">
            <h3>Architecture</h3>
            <p>Iconic urban architecture</p>
        </div>
    </div>

    <!-- ქარდი 8 -->
    <div class="card">
        <img src="images/Photo8.jpeg" alt="Wildlife">
        <div class="card-content">
            <h3>Wildlife</h3>
            <p>Animals in their natural habitat</p>
        </div>
    </div>
</div>

<footer>
    <?php echo date("Y"); ?> Photo Gallery — Made with love by Keto
</footer>

</body>
</html>