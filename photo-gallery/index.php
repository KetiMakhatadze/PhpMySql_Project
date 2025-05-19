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
    <title>ფოტო გალერეა</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>Photo Gallery</h1>
</header>

<?php if ($loggedIn): ?>
    <div class="logout">
        <span>მოგესალმები, <?= htmlspecialchars($username) ?> (<?= htmlspecialchars($role) ?>)</span> |
        <a href="auth/logout.php">გასვლა</a>
    </div>
<?php else: ?>
    <div class="logout">
        <a href="auth/login.php">შესვლა</a> | <a href="auth/register.php">რეგისტრაცია</a>
    </div>
<?php endif; ?>

</body>
</html>
