<?php
session_start();
require_once '../includes/db.php';

// თუ მომხმარებელი უკვე დალოგინებულია, გადავამისამართოთ Home გვერდზე
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php"); // ან home.php, თუ Home გვერდი სხვა ფაილია
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier']);
    $password = $_POST['password'];

    $sql = "SELECT id, username, password FROM users WHERE username = ? OR email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $identifier, $identifier);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = isset($user['role']) ? $user['role'] : 'user';
            header("Location: index.php");
            exit;
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "No account found with that username or email.";
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Gallery - Log in</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <header>
        <h1>Photo Gallery</h1>
        <nav>
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="register.php">Register</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>
    
    <div class="register-container">
        <div class="form-section">
            <h2>Welcome Back to Photo Gallery</h2>
            <p>Log in to your Photo Gallery account</p>
            
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            
            <form method="POST" action="">
                <input type="text" name="identifier" placeholder="Username or email" required>
                <input type="password" name="password" placeholder="Enter password" required>
                <button type="submit">Log in</button>
            </form>
            
            <div class="signin-link">
                Don't have an account yet? <a href="register.php">Sign up now</a>
            </div>
        </div>
        <div class="photo-section"></div>
    </div>
    <footer>
        <?php echo date("Y"); ?> Photo Gallery — Made with love by Keto
    </footer>
</body>
</html>