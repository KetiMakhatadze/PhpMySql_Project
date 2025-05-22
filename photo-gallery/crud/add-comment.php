<?php
    // იწყებს სესიას
    session_start();
    // აკავშირებს db.php ფაილს includes ფოლდერიდან, რომელიც შეიცავს მონაცემთა ბაზასთან დაკავშირების ლოგიკას (მაგ., MySQL-ის კავშირი). require_once უზრუნველყოფს, რომ ფაილი ჩაიტვირთოს მხოლოდ ერთხელ
    require_once '../includes/db.php';
    // ამოწმებს, აკმაყოფილებს თუ არა მოთხოვნა სამ პირობას: მომხმარებელი შესულია (user_id არსებობს), photo_id გაგზავნილია POST-ით, და comment გაგზავნილია POST-ით
    if (isset($_SESSION['user_id']) && isset($_POST['photo_id']) && isset($_POST['comment'])) {
        // იღებს მომხმარებლის ID-ს სესიიდან და გარდაქმნის მას მთელ რიცხვად (int) უსაფრთხოებისთვის
        $user_id = (int)$_SESSION['user_id'];
        // იღებს ფოტოს ID-ს POST-დან და გარდაქმნის მას მთელ რიცხვად (int) უსაფრთხოებისთვის
        $photo_id = (int)$_POST['photo_id'];
        // იღებს კომენტარის ტექსტს POST-დან, trim() შლის ზედმეტ სივრცეებს, mysqli_real_escape_string იცავს SQL ინექციისგან
        $comment = mysqli_real_escape_string($conn, trim($_POST['comment']));
        // ქმნის მიმდინარე თარიღსა და დროს ფორმატში 'Y-m-d H:i:s' (მაგ., 2025-05-22 17:05:00)
        $created_at = date('Y-m-d H:i:s');
        // ქმნის SQL მოთხოვნას, რომელიც ამოწმებს, არსებობს თუ არა ფოტო მონაცემთა ბაზაში მითითებული $photo_id-ით
        $check_query = "SELECT id FROM photos WHERE id = ?";
        // ამზადებს SQL მოთხოვნას (prepared statement) ფოტოს არსებობის შესამოწმებლად, უსაფრთხოებისთვის
        $check_stmt = mysqli_prepare($conn, $check_query);
        // უკავშირებს $photo_id-ს, როგორც მთელ რიცხვს ("i"), SQL მოთხოვნის placeholder-ს (?)
        mysqli_stmt_bind_param($check_stmt, "i", $photo_id);
        // ასრულებს მომზადებულ SQL მოთხოვნას ფოტოს შესამოწმებლად
        mysqli_stmt_execute($check_stmt);
        // იღებს SQL მოთხოვნის შედეგს $check_result ცვლადში
        $check_result = mysqli_stmt_get_result($check_stmt);
        // ამოწმებს, მოიძებნა თუ არა ფოტო (ანუ, აქვს თუ არა შედეგს მინიმუმ ერთი ჩანაწერი)
        if (mysqli_num_rows($check_result) > 0) {
            // ქმნის SQL მოთხოვნას, რომელიც ამატებს ახალ კომენტარს comments ცხრილში, photo_id, user_id, comment_text და created_at ველებით
            $query = "INSERT INTO comments (photo_id, user_id, comment_text, created_at) VALUES (?, ?, ?, ?)";
            // ამზადებს SQL მოთხოვნას (prepared statement) კომენტარის დასამატებლად, უსაფრთხოებისთვის
            $stmt = mysqli_prepare($conn, $query);
            // უკავშირებს $photo_id, $user_id, $comment და $created_at-ს SQL მოთხოვნის placeholder-ებთან ("iiss" ნიშნავს: ორი მთელი რიცხვი, ორი სტრიქონი)
            mysqli_stmt_bind_param($stmt, "iiss", $photo_id, $user_id, $comment, $created_at);
            // ასრულებს მომზადებულ SQL მოთხოვნას კომენტარის დასამატებლად
            mysqli_stmt_execute($stmt);
            // გადამისამართდება photo-details.php-ზე, გადასცემს $photo_id-ს URL-ში, რათა დაბრუნდეს იგივე ფოტოს გვერდზე
            header("Location: ../photo-details.php?id=" . $photo_id);
            // წყვეტს სკრიპტის შესრულებას გადამისამართების შემდეგ
            exit();
        } else {
            // თუ ფოტო არ მოიძებნა, გადამისამართდება მთავარ გვერდზე (index.php)
            header("Location: ../index.php");
            // წყვეტს სკრიპტის შესრულებას გადამისამართების შემდეგ
            exit();
        }
    } else {
        // თუ ყველა საჭირო POST მონაცემი (user_id, photo_id, comment) არ არის გაგზავნილი, იღებს photo_id-ს POST-დან (თუ არსებობს) ან ნაგულისხმევად 0-ს
        $photo_id = isset($_POST['photo_id']) ? (int)$_POST['photo_id'] : 0;
        // გადამისამართდება photo-details.php-ზე, გადასცემს $photo_id-ს URL-ში (ან 0-ს, თუ photo_id არ არის)
        header("Location: ../photo-details.php?id=" . $photo_id);
        // წყვეტს სკრიპტის შესრულებას გადამისამართების შემდეგ
        exit();
    }
    // ხურავს მონაცემთა ბაზასთან კავშირს ($conn), რათა გათავისუფლდეს რესურსები
    mysqli_close($conn);
?>