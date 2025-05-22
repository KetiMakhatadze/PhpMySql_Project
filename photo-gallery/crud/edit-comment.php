<?php
    // იწყებს სესიას
    session_start();
    // აკავშირებს db.php ფაილს includes ფოლდერიდან, რომელიც შეიცავს მონაცემთა ბაზასთან დაკავშირების ლოგიკას (მაგ., MySQL-ის კავშირი). require_once უზრუნველყოფს, რომ ფაილი ჩაიტვირთოს მხოლოდ ერთხელ
    require_once '../includes/db.php';
    // ადგენს HTTP სათაურს, რომელიც მიუთითებს, რომ სკრიპტის გამოხმაურება იქნება JSON ფორმატში
    header('Content-Type: application/json');
    // ამოწმებს, არის თუ არა მომხმარებელი შესული (user_id არსებობს) და არის თუ არა ადმინისტრატორი (role = 'admin' ან is_admin = 1)
    if (!isset($_SESSION['user_id']) || !(
        (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') ||
        (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1)
    )) {
        // თუ მომხმარებელი არ არის შესული ან არ არის ადმინისტრატორი, აბრუნებს JSON-ს შეცდომის შეტყობინებით 'Unauthorized'
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        // წყვეტს სკრიპტის შესრულებას
        exit;
    }
    // ამოწმებს, არის თუ არა მოთხოვნის მეთოდი POST და გაგზავნილია თუ არა comment_id და comment_text
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_id']) && isset($_POST['comment_text'])) {
        // იღებს კომენტარის ID-ს POST-დან და გარდაქმნის მას მთელ რიცხვად (int) უსაფრთხოებისთვის
        $comment_id = (int)$_POST['comment_id'];
        // იღებს ახალ კომენტარის ტექსტს POST-დან, trim() შლის ზედმეტ სივრცეებს
        $new_comment = trim($_POST['comment_text']);
        // ქმნის SQL მოთხოვნას, რომელიც განაახლებს კომენტარის ტექსტს (comment_text) comments ცხრილში, სადაც id შეესაბამება $comment_id-ს
        $sql = "UPDATE comments SET comment_text = ? WHERE id = ?";
        // ამზადებს SQL მოთხოვნას (prepared statement) უსაფრთხოებისთვის, რათა თავიდან იქნას აცილებული SQL ინექცია
        $stmt = mysqli_prepare($conn, $sql);
        // უკავშირებს $new_comment-ს (როგორც სტრიქონს) და $comment_id-ს (როგორც მთელ რიცხვს) SQL მოთხოვნის placeholder-ებთან ("si")
        mysqli_stmt_bind_param($stmt, "si", $new_comment, $comment_id);
        // ასრულებს მომზადებულ SQL მოთხოვნას კომენტარის განსაახლებლად
        mysqli_stmt_execute($stmt);
        // იღებს განახლებული ჩანაწერების რაოდენობას (0, თუ არაფერი შეცვლილა)
        $affected = mysqli_stmt_affected_rows($stmt);
        // ხურავს მომზადებულ SQL მოთხოვნას, რათა გათავისუფლდეს რესურსები
        mysqli_stmt_close($stmt);
        // ამოწმებს, მოხდა თუ არა ცვლილება (ანუ, განახლდა თუ არა კომენტარი)
        if ($affected > 0) {
            // თუ განახლება წარმატებულია, აბრუნებს JSON-ს 'success' სტატუსით
            echo json_encode(['status' => 'success']);
        } else {
            // თუ ცვლილებები არ მოხდა (მაგ., კომენტარი არ მოიძებნა ან ტექსტი უცვლელია), აბრუნებს JSON-ს შეცდომის შეტყობინებით
            echo json_encode(['status' => 'error', 'message' => 'No changes made']);
        }
    } else {
        // თუ მოთხოვნა არ არის POST ან comment_id/comment_text არ არის გაგზავნილი, აბრუნებს JSON-ს შეცდომის შეტყობინებით
        echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    }
    // ხურავს მონაცემთა ბაზასთან კავშირს ($conn), რათა გათავისუფლდეს რესურსები
    mysqli_close($conn);
?>