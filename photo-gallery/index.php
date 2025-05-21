<?php
session_start();
require_once 'includes/db.php';

$loggedIn = isset($_SESSION['user_id']);
$username = $loggedIn ? $_SESSION['username'] : '';
$_SESSION['role'] = $loggedIn ? $_SESSION['role'] : 'user';

$query = "SELECT photos.id, photos.image_path, photos.title, photos.description, categories.name AS category_name 
          FROM photos 
          LEFT JOIN categories ON photos.category_id = categories.id 
          ORDER BY photos.created_at DESC";
$result = mysqli_query($conn, $query);
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
                    <li><span>Hello, <?= htmlspecialchars($username) ?></span></li>
                    <li><a href="auth/contact.php">Contact</a></li>
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
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($photo = mysqli_fetch_assoc($result)): ?>
                <div class="card">
                    <a href="photo-details.php?id=<?= htmlspecialchars($photo['id']) ?>">
                        <img src="<?= htmlspecialchars($photo['image_path']) ?>" alt="<?= htmlspecialchars($photo['title']) ?>">
                    </a>
                    <div class="card-content">
                        <h3><?= htmlspecialchars($photo['title']) ?></h3>
                        <p><?= htmlspecialchars($photo['description']) ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No photos found in the gallery.</p>
        <?php endif; ?>
    </div>
    <footer>
        <?php echo date("Y"); ?> Photo Gallery — Made with love by Keto
    </footer>
</body>
</html>

<?php
mysqli_close($conn);
?>