<?php
session_start();
require_once 'includes/db.php';

if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin' && isset($_GET['id'])) {
    $comment_id = (int)$_GET['id'];
    $query = "SELECT photo_id FROM comments WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $comment_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $comment = mysqli_fetch_assoc($result);

    if ($comment) {
        $delete_query = "DELETE FROM comments WHERE id = ?";
        $delete_stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($delete_stmt, "i", $comment_id);
        mysqli_stmt_execute($delete_stmt);
    }

    header("Location: photo-details.php?id=" . $comment['photo_id']);
    exit();
} else {
    header("Location: index.php");
    exit();
}

mysqli_close($conn);
?>