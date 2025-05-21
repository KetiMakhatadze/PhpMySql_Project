<?php
    session_start();
    require_once '../includes/db.php';

    if (isset($_SESSION['user_id']) && isset($_POST['photo_id']) && isset($_POST['comment'])) {
        $user_id = (int)$_SESSION['user_id'];
        $photo_id = (int)$_POST['photo_id'];
        $comment = mysqli_real_escape_string($conn, trim($_POST['comment']));
        $created_at = date('Y-m-d H:i:s');

        $check_query = "SELECT id FROM photos WHERE id = ?";
        $check_stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($check_stmt, "i", $photo_id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($check_result) > 0) {
            $query = "INSERT INTO comments (photo_id, user_id, comment_text, created_at) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "iiss", $photo_id, $user_id, $comment, $created_at);
            mysqli_stmt_execute($stmt);

            header("Location: ../photo-details.php?id=" . $photo_id);
            exit();
        } else {
            header("Location: ../index.php");
            exit();
        }
    } else {
        $photo_id = isset($_POST['photo_id']) ? (int)$_POST['photo_id'] : 0;
        header("Location: ../photo-details.php?id=" . $photo_id);
        exit();
    }

    mysqli_close($conn);
?>