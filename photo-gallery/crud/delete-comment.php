<?php
    // იწყებს სესიას
    session_start();
    // აკავშირებს db.php ფაილს includes ფოლდერიდან, რომელიც შეიცავს მონაცემთა ბაზასთან დაკავშირების ლოგიკას (მაგ., MySQL-ის კავშირი). require_once უზრუნველყოფს, რომ ფაილი ჩაიტვირთოს მხოლოდ ერთხელ
    require_once '../includes/db.php';
    // ამოწმებს, არის თუ არა მომხმარებელი შესული (user_id არსებობს) და არის თუ არა ადმინისტრატორი (role = 'admin' ან is_admin = 1)
    if (!isset($_SESSION['user_id']) || !(
        (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') ||
        (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1)
    )) {
        // თუ მომხმარებელი არ არის შესული ან არ არის ადმინისტრატორი, აბრუნებს 'error' ტექსტს
        echo 'error';
        // წყვეტს სკრიპტის შესრულებას
        exit;
    }
    // ამოწმებს, არის თუ არა მოთხოვნის მეთოდი POST და გაგზავნილია თუ არა comment_id
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_id'])) {
        // იღებს კომენტარის ID-ს POST-დან და გარდაქმნის მას მთელ რიცხვად (int) უსაფრთხოებისთვის
        $comment_id = (int)$_POST['comment_id'];
        // ქმნის SQL მოთხოვნას, რომელიც შლის კომენტარს comments ცხრილიდან, სადაც id შეესაბამება $comment_id-ს
        $sql = "DELETE FROM comments WHERE id = ?";
        // ამზადებს SQL მოთხოვნას (prepared statement) უსაფრთხოებისთვის, რათა თავიდან იქნას აცილებული SQL ინექცია
        $stmt = mysqli_prepare($conn, $sql);
        // უკავშირებს $comment_id-ს, როგორც მთელ რიცხვს ("i"), SQL მოთხოვნის placeholder-ს (?)
        mysqli_stmt_bind_param($stmt, "i", $comment_id);
        // ასრულებს მომზადებულ SQL მოთხოვნას კომენტარის წასაშლელად
        mysqli_stmt_execute($stmt);
        // აბრუნებს 'success' ტექსტს, რათა AJAX მოთხოვნამ იცოდეს, რომ წაშლა წარმატებული იყო
        echo 'success';
        // ხურავს მომზადებულ SQL მოთხოვნას, რათა გათავისუფლდეს რესურსები
        mysqli_stmt_close($stmt);
    } else {
        // თუ მოთხოვნა არ არის POST ან comment_id არ არის გაგზავნილი, აბრუნებს 'error' ტექსტს
        echo 'error';
    }
    // ხურავს მონაცემთა ბაზასთან კავშირს ($conn), რათა გათავისუფლდეს რესურსები
    mysqli_close($conn);
?>