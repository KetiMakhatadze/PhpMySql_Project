<?php
session_start();
require_once 'includes/db.php';

$photo_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$loggedIn = isset($_SESSION['user_id']);
$username = $loggedIn ? $_SESSION['username'] : '';
$isAdmin = $loggedIn && isset($_SESSION['role']) && $_SESSION['role'] == 'admin';

$query = "SELECT photos.id, photos.image_path, photos.created_at, categories.name AS category_name 
          FROM photos 
          LEFT JOIN categories ON photos.category_id = categories.id 
          WHERE photos.id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $photo_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$photo = mysqli_fetch_assoc($result);

if (!$photo) {
    header("Location: index.php");
    exit();
}

$photoDetails = [
    1 => ['title' => 'Mountains', 'description' => 'A stunning view of a mountain range surrounded by clouds.'],
    2 => ['title' => 'City Lights', 'description' => 'A vibrant night view of a city with colorful lights reflecting on the water.'],
    3 => ['title' => 'Desert', 'description' => 'Golden sandy dunes stretching across a vast desert under the sun.'],
    4 => ['title' => 'Ocean', 'description' => 'A deep blue ocean with sunlight shimmering through the waves.'],
    5 => ['title' => 'Nature', 'description' => 'Lush green forest with a serene coastal view.'],
    6 => ['title' => 'Sunset', 'description' => 'A breathtaking sunset with a colorful sky over the mountains.'],
    7 => ['title' => 'Architecture', 'description' => 'An iconic piece of ancient architecture, showcasing history and craftsmanship.'],
    8 => ['title' => 'Wildlife', 'description' => 'A majestic lion resting in its natural habitat.'],
];

$customTitle = isset($photoDetails[$photo_id]) ? $photoDetails[$photo_id]['title'] : 'Untitled Photo';
$customDescription = isset($photoDetails[$photo_id]) ? $photoDetails[$photo_id]['description'] : 'No description available.';

$comments_query = "SELECT comments.*, users.username 
                  FROM comments 
                  LEFT JOIN users ON comments.user_id = users.id 
                  WHERE comments.photo_id = ? 
                  ORDER BY comments.created_at DESC";
$comments_stmt = mysqli_prepare($conn, $comments_query);
mysqli_stmt_bind_param($comments_stmt, "i", $photo_id);
mysqli_stmt_execute($comments_stmt);
$comments_result = mysqli_stmt_get_result($comments_stmt);
?>

<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($customTitle) ?> - Photo Gallery</title>
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
    <div class="photo-detail-container">
        <div class="photo-section">
            <img src="<?= htmlspecialchars($photo['image_path']) ?>" alt="<?= htmlspecialchars($customTitle) ?>">
            <div class="detail-content">
                <h2><?= htmlspecialchars($customTitle) ?></h2>
                <p><?= htmlspecialchars($customDescription) ?></p>
                <p>Category: <?= htmlspecialchars($photo['category_name']) ?></p>
                <p>Uploaded on: <?= htmlspecialchars($photo['created_at']) ?></p>
            </div>
        </div>
        <div class="comments-section">
            <?php if ($loggedIn): ?>
                <form method="POST" action="./crud/add-comment.php">
                    <textarea name="comment" required placeholder="Write your comment..."></textarea>
                    <input type="hidden" name="photo_id" value="<?= htmlspecialchars($photo_id) ?>">
                    <button type="submit">Submit</button>
                </form>
            <?php else: ?>
                <p>Please <a href="auth/login.php">log in</a> to add a comment.</p>
            <?php endif; ?>
            <h3>Comments</h3>
            <?php if (mysqli_num_rows($comments_result) > 0): ?>
                <?php while ($comment = mysqli_fetch_assoc($comments_result)): ?>
                    <div class="comment">
                        <p><strong><?= htmlspecialchars($comment['username']) ?></strong>: <?= htmlspecialchars($comment['comment_text']) ?></p>
                        <p>Posted on: <?= htmlspecialchars($comment['created_at']) ?></p>
                        <?php if ($isAdmin): ?>
                            <a href="./crud/delete-comment.php?id=<?= htmlspecialchars($comment['id']) ?>">Delete</a>
                            <a href="./crud/edit-comment.php?id=<?= htmlspecialchars($comment['id']) ?>">Edit</a>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No comments yet.</p>
            <?php endif; ?>
        </div>
    </div>
    <footer>
        <?php echo date("Y"); ?> Photo Gallery — Made with love by Keto
    </footer>
</body>
</html>

<?php
    mysqli_stmt_close($stmt);
    mysqli_stmt_close($comments_stmt);
    mysqli_close($conn);
?>