<?php
// იწყებს სესიას
session_start();
// აკავშირებს db.php ფაილს includes ფოლდერიდან, რომელიც შეიცავს მონაცემთა ბაზასთან დაკავშირების ლოგიკას (მაგ., MySQL-ის კავშირი). require_once უზრუნველყოფს, რომ ფაილი ჩაიტვირთოს მხოლოდ ერთხელ
require_once '../includes/db.php';
// ამოწმებს, არის თუ არა მოთხოვნის მეთოდი POST (ანუ, ფორმა გაგზავნილია)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // იღებს მომხმარებლის სახელს POST-დან, trim() შლის ზედმეტ სივრცეებს
    $username = trim($_POST['username']);
    // იღებს ელფოსტას POST-დან, trim() შლის ზედმეტ სივრცეებს
    $email = trim($_POST['email']);
    // იღებს პაროლს POST-დან და ჰეშავს მას password_hash ფუნქციის გამოყენებით, PASSWORD_DEFAULT ალგორითმით
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    // ქმნის SQL მოთხოვნას, რომელიც ამატებს ახალ მომხმარებელს users ცხრილში, username, email და password ველებით
    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    // ამზადებს SQL მოთხოვნას (prepared statement) უსაფრთხოებისთვის, რათა თავიდან იქნას აცილებული SQL ინექცია
    $stmt = mysqli_prepare($conn, $sql);
    // უკავშირებს $username, $email და $password-ს, როგორც სტრიქონებს ("sss"), SQL მოთხოვნის placeholder-ებთან (?)
    mysqli_stmt_bind_param($stmt, "sss", $username, $email, $password);
    // თუ SQL მოთხოვნა წარმატებით შესრულდა, გადამისამართდება login.php-ზე
    if (mysqli_stmt_execute($stmt)) {
        // გადამისამართდება login.php-ზე წარმატებული რეგისტრაციის შემდეგ
        header("Location: login.php");
        // წყვეტს სკრიპტის შესრულებას გადამისამართების შემდეგ
        exit;
    } else {
        // თუ რეგისტრაცია ჩაიშალა, ინახავს შეცდომის შეტყობინებას $error ცვლადში
        $error = "Registration failed. Please try again.";
    }
    // ხურავს მომზადებულ SQL მოთხოვნას, რათა გათავისუფლდეს რესურსები
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
            <!-- თუ $error ცვლადი არსებობს, აჩვენებს შეცდომის შეტყობინებას error კლასით -->
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <!-- ფორმა რეგისტრაციისთვის, რომელიც POST მეთოდით გაგზავნის მონაცემებს -->
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
        <!-- აჩვენებს მიმდინარე წელს (date("Y")) და საავტორო შეტყობინებას -->
        <?php echo date("Y"); ?> Photo Gallery — Made with love by Keto
    </footer>
</body>
</html>