<?php
    // იწყებს სესიას
    session_start();
    // აკავშირებს db.php ფაილს includes ფოლდერიდან, რომელიც შეიცავს მონაცემთა ბაზასთან დაკავშირების ლოგიკას. require_once უზრუნველყოფს, რომ ფაილი ჩაიტვირთოს მხოლოდ ერთხელ
    require_once '../includes/db.php';
    // ამოწმებს, არის თუ არა მომხმარებელი უკვე შესული სისტემაში user_id-ის არსებობის მიხედვით
    if (isset($_SESSION['user_id'])) {
        // თუ მომხმარებელი შესულია, გადამისამართდება index.php-ზე
        header("Location: ../index.php");
        // წყვეტს სკრიპტის შესრულებას გადამისამართების შემდეგ
        exit;
    }
    // ამოწმებს, არის თუ არა მოთხოვნის მეთოდი POST (ანუ, ფორმა გაგზავნილია)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // იღებს identifier-ს (მომხმარებლის სახელი ან ელფოსტა) POST-დან, trim() შლის ზედმეტ სივრცეებს
        $identifier = trim($_POST['identifier']);
        // იღებს პაროლს POST-დან
        $password = $_POST['password'];
        // ქმნის SQL მოთხოვნას, რომელიც იღებს მომხმარებლის id, username, password, role და is_admin-ს users ცხრილიდან, სადაც username ან email ემთხვევა $identifier-ს
        $sql = "SELECT id, username, password, role, is_admin FROM users WHERE username = ? OR email = ?";
        // ამზადებს SQL მოთხოვნას (prepared statement) უსაფრთხოებისთვის, რათა თავიდან იქნას აცილებული SQL ინექცია
        $stmt = mysqli_prepare($conn, $sql);
        // უკავშირებს $identifier-ს ორჯერ (username-ისა და email-ისთვის), როგორც სტრიქონს ("ss"), SQL მოთხოვნის placeholder-ებთან (?)
        mysqli_stmt_bind_param($stmt, "ss", $identifier, $identifier);
        // ასრულებს მომზადებულ SQL მოთხოვნას
        mysqli_stmt_execute($stmt);
        // იღებს SQL მოთხოვნის შედეგს $result ცვლადში
        $result = mysqli_stmt_get_result($stmt);
        // თუ მომხმარებელი მოიძებნა, ინახავს მის მონაცემებს $user ცვლადში, როგორც ასოციაციურ მასივს
        if ($user = mysqli_fetch_assoc($result)) {
            // ამოწმებს, ემთხვევა თუ არა შეყვანილი პაროლი მონაცემთა ბაზაში შენახულ ჰეშირებულ პაროლს
            if (password_verify($password, $user['password'])) {
                // თუ პაროლი სწორია, ინახავს მომხმარებლის ID-ს სესიაში
                $_SESSION['user_id'] = $user['id'];
                // ინახავს მომხმარებლის სახელს სესიაში
                $_SESSION['username'] = $user['username'];
                // ინახავს მომხმარებლის როლს (მაგ., admin ან user) სესიაში
                $_SESSION['role'] = $user['role'];
                // ინახავს is_admin მნიშვნელობას (0 ან 1) სესიაში
                $_SESSION['is_admin'] = $user['is_admin'];
                // გადამისამართდება index.php-ზე წარმატებული ავტორიზაციის შემდეგ
                header("Location: ../index.php");
                // წყვეტს სკრიპტის შესრულებას გადამისამართების შემდეგ
                exit;
            } else {
                // თუ პაროლი არასწორია, ინახავს შეცდომის შეტყობინებას $error ცვლადში
                $error = "Incorrect password.";
            }
        } else {
            // თუ მომხმარებელი არ მოიძებნა, ინახავს შეცდომის შეტყობინებას $error ცვლადში
            $error = "No account found with that username or email.";
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
            <!-- თუ $error ცვლადი არსებობს, აჩვენებს შეცდომის შეტყობინებას error კლასით -->
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <!-- ფორმა ავტორიზაციისთვის, რომელიც POST მეთოდით გაგზავნის მონაცემებს -->
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
        <!-- აჩვენებს მიმდინარე წელს (date("Y")) და საავტორო შეტყობინებას -->
        <?php echo date("Y"); ?> Photo Gallery — Made with love by Keto
    </footer>
</body>
</html>