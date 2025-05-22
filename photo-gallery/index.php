<?php
    // იწყებს სესიას
    session_start();
    // აკავშირებს db.php ფაილს, რომელიც შეიცავს მონაცემთა ბაზასთან დაკავშირების ლოგიკას. require_once უზრუნველყოფს, რომ ფაილი ჩაიტვირთოს მხოლოდ ერთხელ
    require_once 'includes/db.php';
    // ამოწმებს, არის თუ არა მომხმარებელი შესული სისტემაში user_id-ის არსებობის მიხედვით. $loggedIn იქნება true, თუ user_id არსებობს, ან false, თუ არ არსებობს
    $loggedIn = isset($_SESSION['user_id']);
    // თუ მომხმარებელი შესულია ($loggedIn = true), $username იღებს სესიიდან username-ის მნიშვნელობას, წინააღმდეგ შემთხვევაში ცარიელი სტრიქონი ('')
    $username = $loggedIn ? $_SESSION['username'] : '';
    // თუ მომხმარებელი შესულია, სესიის role იღებს არსებულ role-ს (მაგ., admin ან user), თუ არა, ენიჭება ნაგულისხმევი მნიშვნელობა 'user'
    $_SESSION['role'] = $loggedIn ? $_SESSION['role'] : 'user';
    // ქმნის SQL მოთხოვნას, რომელიც იღებს ფოტოების id, image_path, title, description და კატეგორიის სახელს (category_name) photos და categories ცხრილებიდან. LEFT JOIN უზრუნველყოფს, რომ ყველა ფოტო გამოჩნდეს, მიუხედავად იმისა, აქვს თუ არა კატეგორია. ORDER BY created_at DESC ახარისხებს ფოტოებს შექმნის თარიღის მიხედვით კლებადობით
    $query = "SELECT photos.id, photos.image_path, photos.title, photos.description, categories.name AS category_name 
            FROM photos 
            LEFT JOIN categories ON photos.category_id = categories.id 
            ORDER BY photos.created_at DESC";
    // ასრულებს SQL მოთხოვნას მონაცემთა ბაზაში ($conn კავშირის ობიექტია db.php-დან) და შედეგს ინახავს $result ცვლადში
    $result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <title>Photo Gallery</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Photo Gallery</h1>
        <nav>
            <ul>
                <!-- თუ მომხმარებელი შესულია ($loggedIn = true), აჩვენებს მისასალმებელ შეტყობინებას და შესაბამის ბმულებს -->
                <?php if ($loggedIn): ?>
                    <!-- აჩვენებს მომხმარებლის სახელს, htmlspecialchars გამოიყენება XSS თავდასხმების თავიდან ასაცილებლად -->
                    <li><span>Hello, <?= htmlspecialchars($username) ?></span></li>
                    <!-- ბმული "Contact" გვერდისკენ, auth/contact.php-ზე -->
                    <li><a href="auth/contact.php">Contact</a></li>
                    <!-- ბმული "Log out" გვერდისკენ, auth/logout.php-ზე, რომელიც გამოიყენება სისტემიდან გამოსასვლელად -->
                    <li><a href="auth/logout.php">Log out</a></li>
                <!-- თუ მომხმარებელი არ არის შესული, აჩვენებს სხვა ბმულებს -->
                <?php else: ?>
                    <!-- ბმული "Register" გვერდისკენ, auth/register.php-ზე, რეგისტრაციისთვის -->
                    <li><a href="auth/register.php">Register</a></li>
                    <!-- ბმული "Log in" გვერდისკენ, auth/login.php-ზე, სისტემაში შესასვლელად -->
                    <li><a href="auth/login.php">Log in</a></li>
                    <!-- ბმული "Contact" გვერდისკენ, auth/contact.php-ზე -->
                    <li><a href="auth/contact.php">Contact</a></li>
                <!-- if პირობის დასასრული -->
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <div class="gallery-grid">
        <!-- ამოწმებს, აქვს თუ არა SQL მოთხოვნის შედეგს ($result) მონაცემები (ფოტოები) -->
        <?php if (mysqli_num_rows($result) > 0): ?>
            <!-- ციკლი, რომელიც გადის თითოეულ ფოტოზე SQL შედეგიდან, ინახავს მონაცემებს $photo ცვლადში -->
            <?php while ($photo = mysqli_fetch_assoc($result)): ?>
                <div class="card">
                    <!-- ბმული ფოტოს დეტალურ გვერდზე (photo-details.php), id გადაეცემა URL-ში -->
                    <a href="photo-details.php?id=<?= htmlspecialchars($photo['id']) ?>">
                        <!-- ფოტოს გამოსახულება, src-ში მითითებულია image_path, alt-ში title, htmlspecialchars ხელს უშლის XSS თავდასხმებს -->
                        <img src="<?= htmlspecialchars($photo['image_path']) ?>" alt="<?= htmlspecialchars($photo['title']) ?>">
                    </a>
                    <div class="card-content">
                        <!-- ფოტოს სათაური, htmlspecialchars გამოიყენება უსაფრთხოებისთვის -->
                        <h3><?= htmlspecialchars($photo['title']) ?></h3>
                        <!-- ფოტოს აღწერა, htmlspecialchars გამოიყენება უსაფრთხოებისთვის -->
                        <p><?= htmlspecialchars($photo['description']) ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <!-- თუ ფოტოები არ მოიძებნა, აჩვენებს შეტყობინებას -->
        <?php else: ?>
            <!-- შეტყობინება, თუ გალერეაში ფოტოები არ არის -->
            <p>No photos found in the gallery.</p>
        <!-- if პირობის დასასრული -->
        <?php endif; ?>
    </div>
    <footer>
        <!-- აჩვენებს მიმდინარე წელს (date("Y")) და საავტორო შეტყობინებას -->
        <?php echo date("Y"); ?> Photo Gallery — Made with love by Keto
    </footer>
</body>
</html>

<?php
    // ხურავს მონაცემთა ბაზასთან კავშირს ($conn), რათა გათავისუფლდეს რესურსები
    mysqli_close($conn);
?>