<?php
session_start();
require_once 'includes/db.php';

if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin' && isset($_GET['id'])) {
    $comment_id = (int)$_GET['id'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
        $comment_text = mysqli_real_escape_string($conn, trim($_POST['comment']));
        $update_query = "UPDATE comments SET comment_text = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "si", $comment_text, $comment_id);
        mysqli_stmt_execute($update_stmt);

        $query = "SELECT photo_id FROM comments WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $comment_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $comment = mysqli_fetch_assoc($result);

        header("Location: photo-details.php?id=" . $comment['photo_id']);
        exit();
    }

    $query = "SELECT * FROM comments WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $comment_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $comment = mysqli_fetch_assoc($result);
    $photo_id = $comment['photo_id'];
?>
<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <title>Edit Comment</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Photo Gallery</h1>
        <nav>
            <ul>
                <li><a href="photo-details.php?id=<?= htmlspecialchars($photo_id) ?>">Back</a></li>
            </ul>
        </nav>
    </header>
    <div class="photo-detail">
        <form method="POST" action="">
            <textarea name="comment" required><?= htmlspecialchars($comment['comment_text']) ?></textarea>
            <input type="hidden" name="photo_id" value="<?= htmlspecialchars($photo_id) ?>">
            <button type="submit">Save</button>
        </form>
    </div>
    <footer>
        <?php echo date("Y"); ?> Photo Gallery — Made with love by Keto
    </footer>
</body>
</html>
<?php
    mysqli_close($conn);
} else {
    header("Location: index.php");
    exit();
}
?>