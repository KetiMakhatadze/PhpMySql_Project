<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $username, $email, $password);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: login.php");
        exit;
    } else {
        $error = "Registration failed. Please try again.";
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Gallery - Registration</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <header>
        <h1>Photo Gallery</h1>
        <nav>
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="login.php">Log in</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>
    
    <div class="register-container">
        <div class="form-section">
            <h2>Welcome to the Photo Gallery</h2>
            <p>Create your Photo Gallery account</p>
            
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            
            <form method="POST" action="">
                <input type="text" name="username" placeholder="Your name" required>
                <input type="email" name="email" placeholder="Your email address" required>
                <input type="password" name="password" placeholder="Create password" required>
                <button type="submit">Sign up</button>
            </form>
            
            <div class="signin-link">
                Already have an account yet? <a href="login.php">Sign in now</a>
            </div>
        </div>
        <div class="photo-section"></div>
    </div>
    <footer>
        <?php echo date("Y"); ?> Photo Gallery — Made with love by Keto
    </footer>
</body>
</html>