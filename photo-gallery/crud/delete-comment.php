<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || !(
    (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') ||
    (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1)
)) {
    echo 'error';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_id'])) {
    $comment_id = (int)$_POST['comment_id'];
    $sql = "DELETE FROM comments WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $comment_id);
    mysqli_stmt_execute($stmt);
    echo 'success';
    mysqli_stmt_close($stmt);
} else {
    echo 'error';
}
mysqli_close($conn);
?>