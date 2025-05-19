<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if (empty($name) || empty($email) || empty($message)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } else {
        $sql = "INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $message);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Your message has been sent successfully!";
        } else {
            $error = "Failed to send your message. Please try again.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Gallery - Contact</title>
    <link rel="stylesheet" href="contact.css">
</head>
<body>
    <header>
        <h1>Photo Gallery</h1>
        <nav>
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="login.php">Log in</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </nav>
    </header>
    
    <div class="register-container">
        <div class="form-section">
            <h2>Contact Us</h2>
            <p>Send us a message and we'll get back to you soon!</p>
            
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
            
            <form method="POST" action="">
                <input type="text" name="name" placeholder="Your name" required>
                <input type="email" name="email" placeholder="Your email address" required>
                <textarea name="message" placeholder="Your message" required></textarea>
                <button type="submit">Send Message</button>
            </form>
            
            <div class="signin-link">
                Need an account? <a href="register.php">Sign up now</a>
            </div>
        </div>
    </div>
    <footer>
        <?php echo date("Y"); ?> Photo Gallery — Made with love by Keto
    </footer>
</body>
</html>