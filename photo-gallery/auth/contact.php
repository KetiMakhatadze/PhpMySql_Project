<?php
    // იწყებს სესიას
    session_start();
    // აკავშირებს db.php ფაილს includes ფოლდერიდან, რომელიც შეიცავს მონაცემთა ბაზასთან დაკავშირების ლოგიკას (მაგ., MySQL-ის კავშირი). require_once უზრუნველყოფს, რომ ფაილი ჩაიტვირთოს მხოლოდ ერთხელ
    require_once '../includes/db.php';
    // ამოწმებს, არის თუ არა მოთხოვნის მეთოდი POST (ანუ, ფორმა გაგზავნილია)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // იღებს სახელს POST-დან, trim() შლის ზედმეტ სივრცეებს
        $name = trim($_POST['name']);
        // იღებს ელფოსტას POST-დან, trim() შლის ზედმეტ სივრცეებს
        $email = trim($_POST['email']);
        // იღებს შეტყობინებას POST-დან, trim() შლის ზედმეტ სივრცეებს
        $message = trim($_POST['message']);
        // ამოწმებს, არის თუ არა რომელიმე ველი ცარიელი (სახელი, ელფოსტა, შეტყობინება)
        if (empty($name) || empty($email) || empty($message)) {
            // თუ რომელიმე ველი ცარიელია, ინახავს შეცდომის შეტყობინებას $error ცვლადში
            $error = "All fields are required.";
        // ამოწმებს, არის თუ არა ელფოსტის ფორმატი სწორი filter_var ფუნქციის გამოყენებით
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // თუ ელფოსტის ფორმატი არასწორია, ინახავს შეცდომის შეტყობინებას $error ცვლადში
            $error = "Invalid email address.";
        } else {
            // ქმნის SQL მოთხოვნას, რომელიც ამატებს ახალ შეტყობინებას contacts ცხრილში, name, email და message ველებით
            $sql = "INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)";
            // ამზადებს SQL მოთხოვნას (prepared statement) უსაფრთხოებისთვის, რათა თავიდან იქნას აცილებული SQL ინექცია
            $stmt = mysqli_prepare($conn, $sql);
            // უკავშირებს $name, $email და $message-ს, როგორც სტრიქონებს ("sss"), SQL მოთხოვნის placeholder-ებთან (?)
            mysqli_stmt_bind_param($stmt, "sss", $name, $email, $message);
            // თუ SQL მოთხოვნა წარმატებით შესრულდა, ინახავს წარმატების შეტყობინებას $success ცვლადში
            if (mysqli_stmt_execute($stmt)) {
                // ინახავს წარმატების შეტყობინებას $success ცვლადში
                $success = "Your message has been sent successfully!";
            } else {
                // თუ შეტყობინების გაგზავნა ჩაიშალა, ინახავს შეცდომის შეტყობინებას $error ცვლადში
                $error = "Failed to send your message. Please try again.";
            }
            // ხურავს მომზადებულ SQL მოთხოვნას, რათა გათავისუფლდეს რესურსები
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
            </ul>
        </nav>
    </header>
    <div class="register-container">
        <div class="form-section">
            <h2>Contact Us</h2>
            <p>Send us a message and we'll get back to you soon!</p>
            <!-- თუ $error ცვლადი არსებობს, აჩვენებს შეცდომის შეტყობინებას error კლასით -->
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <!-- თუ $success ცვლადი არსებობს, აჩვენებს წარმატების შეტყობინებას success კლასით -->
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
        <!-- აჩვენებს მიმდინარე წელს (date("Y")) და საავტორო შეტყობინებას -->
        <?php echo date("Y"); ?> Photo Gallery — Made with love by Keto
    </footer>
</body>
</html>