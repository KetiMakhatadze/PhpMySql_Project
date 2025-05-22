<?php
    session_start();
    require_once '../includes/db.php';

    header('Content-Type: application/json');

    if (!isset($_SESSION['user_id']) || !(
        (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') ||
        (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1)
    )) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_id']) && isset($_POST['comment_text'])) {
        $comment_id = (int)$_POST['comment_id'];
        $new_comment = trim($_POST['comment_text']);
        
        $sql = "UPDATE comments SET comment_text = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $new_comment, $comment_id);
        mysqli_stmt_execute($stmt);
        $affected = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);

        if ($affected > 0) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No changes made']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    }
    mysqli_close($conn);
?>