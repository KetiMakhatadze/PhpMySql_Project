<?php
    // იწყებს PHP კოდის განყოფილებას, რომელიც მიუთითებს, რომ კოდი PHP-ის ძრავით უნდა შესრულდეს
    session_start();
    // აკავშირებს db.php ფაილს, რომელიც შეიცავს მონაცემთა ბაზასთან დაკავშირების ლოგიკას (მაგ., MySQL-ის კავშირი). require_once უზრუნველყოფს, რომ ფაილი ჩაიტვირთოს მხოლოდ ერთხელ
    require_once 'includes/db.php';
    // იღებს photo_id-ს URL-დან ($_GET['id']) და გარდაქმნის მას მთელ რიცხვად (int). თუ id არ არის მითითებული, $photo_id ნაგულისხმევად 0-ს იღებს
    $photo_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    // ამოწმებს, არის თუ არა მომხმარებელი შესული სისტემაში user_id-ის არსებობის მიხედვით. $loggedIn იქნება true, თუ user_id არსებობს, ან false, თუ არ არსებობს
    $loggedIn = isset($_SESSION['user_id']);
    // თუ მომხმარებელი შესულია ($loggedIn = true), $username იღებს სესიიდან username-ის მნიშვნელობას, წინააღმდეგ შემთხვევაში ცარიელი სტრიქონი ('')
    $username = $loggedIn ? $_SESSION['username'] : '';
    // ამოწმებს, არის თუ არა მომხმარებელი ადმინისტრატორი. $isAdmin იქნება true, თუ მომხმარებელი შესულია და მისი role არის 'admin' ან is_admin არის 1
    $isAdmin = $loggedIn && (
        (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') ||
        (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1)
    );
    // ქმნის SQL მოთხოვნას, რომელიც იღებს ფოტოს id, image_path, created_at და კატეგორიის სახელს (category_name) photos და categories ცხრილებიდან, სადაც photos.id შეესაბამება $photo_id-ს
    $query = "SELECT photos.id, photos.image_path, photos.created_at, categories.name AS category_name 
            FROM photos 
            LEFT JOIN categories ON photos.category_id = categories.id 
            WHERE photos.id = ?";
    // ამზადებს SQL მოთხოვნას (prepared statement) უსაფრთხოებისთვის, რათა თავიდან იქნას აცილებული SQL ინექცია
    $stmt = mysqli_prepare($conn, $query);
    // უკავშირებს $photo_id-ს, როგორც მთელ რიცხვს ("i"), SQL მოთხოვნის placeholder-ს (?)
    mysqli_stmt_bind_param($stmt, "i", $photo_id);
    // ასრულებს მომზადებულ SQL მოთხოვნას
    mysqli_stmt_execute($stmt);
    // იღებს SQL მოთხოვნის შედეგს $result ცვლადში
    $result = mysqli_stmt_get_result($stmt);
    // იღებს ფოტოს მონაცემებს, როგორც ასოციაციურ მასივს ($photo)
    $photo = mysqli_fetch_assoc($result);
    // თუ ფოტო არ მოიძებნა ($photo არის false), გადამისამართდება index.php-ზე და წყვეტს სკრიპტის შესრულებას
    if (!$photo) {
        header("Location: index.php");
        exit();
    }
    // ქმნის $photoDetails მასივს, რომელიც შეიცავს ფოტოების სათაურებსა და აღწერებს ხელით განსაზღვრული ფოტოებისთვის (ID 1-დან 8-მდე)
    $photoDetails = [
        1 => ['title' => 'Mount Everest', 'description' => 'Mount Everest, known officially and locally as Sagarmatha in Nepal or Qomolangma in Tibet, is Earth\'s highest mountain above sea level, located in the Mahalangur Himal sub-range of the Himalayas. The China–Nepal border runs across its summit point. Its elevation (snow height) of 8,848.86 m was most recently established in 2020 by the Chinese and Nepali authorities. Mount Everest attracts many climbers, including highly experienced mountaineers. There are two main climbing routes, one approaching the summit from the southeast in Nepal (known as the standard route) and the other from the north in Tibet. While not posing substantial technical climbing challenges on the standard route, Everest presents dangers such as altitude sickness, weather, and wind, as well as hazards from avalanches and the Khumbu Icefall. As of May 2024, 340 people have died on Everest.'],
        2 => ['title' => 'Ho Chi Minh City', 'description' => 'Ho Chi Minh City (HCMC), commonly known as Saigon, is the most populous city in Vietnam with a population of around 10 million in 2023. The city\'s geography is defined by rivers and canals, of which the largest is Saigon River. As a municipality, Ho Chi Minh City consists of 16 urban districts, five rural districts, and one municipal city (sub-city). As the largest financial centre in Vietnam, Ho Chi Minh City has the largest gross regional domestic product out of all Vietnam provinces and municipalities, contributing around a quarter of the country\'s total GDP. Ho Chi Minh City\'s metropolitan area is ASEAN\'s 5th largest economy, also the biggest outside an ASEAN country capital.'],
        3 => ['title' => 'Sossusvlei', 'description' => 'Sossusvlei (sometimes written Sossus Vlei) is a salt and clay pan surrounded by high red dunes, located in the southern part of the Namib Desert, in the Namib-Naukluft National Park of Namibia. The name "Sossusvlei" is often used in an extended meaning to refer to the surrounding area (including other neighbouring vleis such as Deadvlei and other high dunes). These landmarks are some of the major visitor attractions of Namibia. The name "Sossusvlei" is of mixed origin and roughly means "dead-end marsh". Vlei is the Afrikaans word for "marsh", while "sossus" is Nama for "no return" or "dead end". Sossusvlei owes this name to the fact that it is an endorheic drainage basin (i.e., a drainage basin without outflows) for the ephemeral Tsauchab River.'],
        4 => ['title' => 'Blue maomao', 'description' => 'The blue maomao (Scorpis violacea), also known as the violet sweep, blue sweep or hardbelly, is a species of marine ray-finned fish, a member of the subfamily Scorpidinae, part of the sea chub family Kyphosidae. It is native to the southwestern Pacific Ocean from Australia to New Zealand and the Kermadec Islands, where it can be found in inshore waters from the surface to depths of 30 m (98 ft). This fish can reach a length of 40 cm (16 in). It is commercially important and is also a popular game fish. The blue maomao has a laterally, compressed and relatively deep body with a noticeably forked tail. They have protrusible jaws, equipped with a number of rows of small, closely set teeth, which are used to capture larger zooplankton. The adults are deep blue dorsally and pale ventrally, at night they change colour to a mottled dark green. The juveniles are grey with a yellow anal fin. They can grow to a fork length of 45 centimetres (18 in).'],
        5 => ['title' => 'Amazon rainforest', 'description' => 'The Amazon rainforest, also called the Amazon jungle or Amazonia, is a moist broadleaf tropical rainforest in the Amazon biome that covers most of the Amazon basin of South America. This basin encompasses 7,000,000 km2 (2,700,000 sq mi), of which 6,000,000 km2 (2,300,000 sq mi) are covered by the rainforest. This region includes territory belonging to nine nations and 3,344 indigenous territories. The majority of the forest, 60%, is in Brazil, followed by Peru with 13%, Colombia with 10%, and with minor amounts in Bolivia, Ecuador, French Guiana, Guyana, Suriname, and Venezuela. Four nations have "Amazonas" as the name of one of their first-level administrative regions, and France uses the name "Guiana Amazonian Park" for French Guiana\'s protected rainforest area. The Amazon represents over half of the total area of remaining rainforests on Earth, and comprises the largest and most biodiverse tract of tropical rainforest in the world, with an estimated 390 billion individual trees in about 16,000 species.'],
        6 => ['title' => 'Aurora', 'description' => 'An aurora (pl. aurorae or auroras), also commonly known as the northern lights (aurora borealis) or southern lights (aurora australis), is a natural light display in Earth\'s sky, predominantly observed in high-latitude regions (around the Arctic and Antarctic). Auroras display dynamic patterns of radiant lights that appear as curtains, rays, spirals or dynamic flickers covering the entire sky. Auroras are the result of disturbances in the Earth\'s magnetosphere caused by enhanced speeds of solar wind from coronal holes and coronal mass ejections. These disturbances alter the trajectories of charged particles in the magnetospheric plasma. These particles, mainly electrons and protons, precipitate into the upper atmosphere (thermosphere/exosphere). The resulting ionization and excitation of atmospheric constituents emit light of varying color and complexity. The form of the aurora, occurring within bands around both polar regions, is also dependent on the amount of acceleration imparted to the precipitating particles.'],
        7 => ['title' => 'Colosseum', 'description' => 'The Colosseum is an elliptical amphitheatre in the centre of the city of Rome, Italy, just east of the Roman Forum. It is the largest ancient amphitheatre ever built, and is still the largest standing amphitheatre in the world, despite its age. Construction began under the Emperor Vespasian in 72 and was completed in AD 80 under his successor and heir, Titus. Further modifications were made during the reign of Domitian. The three emperors who were patrons of the work are known as the Flavian dynasty, and the amphitheatre was named the Flavian Amphitheatre by later classicists and archaeologists for its association with their family name (Flavius). The Colosseum is built of travertine limestone, tuff (volcanic rock), and brick-faced concrete. It could hold an estimated 50,000 to 80,000 spectators at various points in its history, having an average audience of some 65,000;'],
        8 => ['title' => 'Lion', 'description' => 'The lion (Panthera leo) is a large cat of the genus Panthera, native to Sub-Saharan Africa and India. It has a muscular, broad-chested body; a short, rounded head; round ears; and a dark, hairy tuft at the tip of its tail. It is sexually dimorphic; adult male lions are larger than females and have a prominent mane. It is a social species, forming groups called prides. A lion\'s pride consists of a few adult males, related females, and cubs. Groups of female lions usually hunt together, preying mostly on medium-sized and large ungulates. The lion is an apex and keystone predator. The lion inhabits grasslands, savannahs, and shrublands. It is usually more diurnal than other wild cats, but when persecuted, it adapts to being active at night and at twilight. During the Neolithic period, the lion ranged throughout Africa and Eurasia, from Southeast Europe to India, but it has been reduced to fragmented populations in sub-Saharan Africa and one population in western India.'],
    ];
    // თუ $photoDetails შეიცავს $photo_id-ის შესაბამის სათაურს, იღებს მას, წინააღმდეგ შემთხვევაში ნაგულისხმევად 'Untitled Photo'
    $customTitle = isset($photoDetails[$photo_id]) ? $photoDetails[$photo_id]['title'] : 'Untitled Photo';
    // თუ $photoDetails შეიცავს $photo_id-ის შესაბამის აღწერას, იღებს მას, წინააღმდეგ შემთხვევაში ნაგულისხმევად 'No description available.'
    $customDescription = isset($photoDetails[$photo_id]) ? $photoDetails[$photo_id]['description'] : 'No description available.';
    // ქმნის SQL მოთხოვნას, რომელიც იღებს კომენტარებს (comments.*), მომხმარებლის სახელს (username) comments და users ცხრილებიდან, სადაც photo_id შეესაბამება $photo_id-ს, და ახარისხებს კლებადობით created_at-ის მიხედვით
    $comments_query = "SELECT comments.*, users.username 
                    FROM comments 
                    LEFT JOIN users ON comments.user_id = users.id 
                    WHERE comments.photo_id = ? 
                    ORDER BY comments.created_at DESC";
    // ამზადებს SQL მოთხოვნას (prepared statement) კომენტარებისთვის უსაფრთხოებისთვის
    $comments_stmt = mysqli_prepare($conn, $comments_query);
    // უკავშირებს $photo_id-ს, როგორც მთელ რიცხვს ("i"), კომენტარების SQL მოთხოვნის placeholder-ს (?)
    mysqli_stmt_bind_param($comments_stmt, "i", $photo_id);
    // ასრულებს მომზადებულ SQL მოთხოვნას კომენტარებისთვის
    mysqli_stmt_execute($comments_stmt);
    // იღებს კომენტარების SQL მოთხოვნის შედეგს $comments_result ცვლადში
    $comments_result = mysqli_stmt_get_result($comments_stmt);
?>

<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($customTitle) ?> - Photo Gallery</title>
    <link rel="stylesheet" href="style.css">
    <!-- აკავშირებს jQuery ბიბლიოთეკას CDN-დან, რომელიც გამოიყენება AJAX მოთხოვნებისთვის -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // ასრულებს კოდს, როდესაც DOM (გვერდის სტრუქტურა) სრულად ჩაიტვირთება
        $(document).ready(function() {
            // ამატებს მოვლენის მსმენელს (event listener) წაშლის ღილაკებზე (delete-comment) კლასის მქონე ელემენტებისთვის
            $('.delete-comment').on('click', function(e) {
                // ხელს უშლის ფორმის ნაგულისხმევ გაგზავნას, რათა გვერდი არ გადაიტვირთოს
                e.preventDefault();
                // აჩვენებს დადასტურების ფანჯარას კომენტარის წაშლისთვის
                if (confirm('Are you sure you want to delete this comment?')) {
                    // პოულობს უახლოეს ფორმას, რომელიც შეიცავს წაშლის ღილაკს
                    var form = $(this).closest('form');
                    // აგზავნის AJAX მოთხოვნას ფორმის URL-ზე (action) POST მეთოდით
                    $.ajax({
                        // ფორმის action ატრიბუტის მნიშვნელობა (მაგ., delete-comment.php)
                        url: form.attr('action'),
                        // მოთხოვნის ტიპი (POST)
                        type: 'POST',
                        // ფორმის სერიალიზებული მონაცემები (მაგ., comment_id)
                        data: form.serialize(),
                        // თუ მოთხოვნა წარმატებულია, ამოწმებს სერვერის პასუხს
                        success: function(response) {
                            // თუ პასუხი არის 'success', შლის კომენტარის div-ს გვერდიდან
                            if (response === 'success') {
                                form.closest('.comment').remove();
                            // თუ პასუხი არ არის 'success', აჩვენებს შეცდომის შეტყობინებას
                            } else {
                                alert('Error deleting comment.');
                            }
                        },
                        // თუ AJAX მოთხოვნა ვერ შესრულდა, აჩვენებს შეცდომის შეტყობინებას
                        error: function() {
                            alert('An error occurred.');
                        }
                    });
                }
            });
            // ამატებს მოვლენის მსმენელს (event listener) რედაქტირების ღილაკებზე (edit-comment) კლასის მქონე ელემენტებისთვის
            $('.comment').on('click', '.edit-comment', function(e) {
                // ხელს უშლის ნაგულისხმევ ქცევას (მაგ., ბმულის გადამისამართებას)
                e.preventDefault();
                // პოულობს უახლოეს div-ს comment კლასით
                var commentDiv = $(this).closest('.comment');
                // იღებს კომენტარის ID-ს data-comment-id ატრიბუტიდან
                var commentId = $(this).data('comment-id');
                // იღებს კომენტარის ამჟამინდელ ტექსტს, შლის მომხმარებლის სახელს (strong თეგიდან)
                var currentText = commentDiv.find('p:first').text().replace(commentDiv.find('strong').text() + ': ', '');
                // ქმნის HTML ფორმას კომენტარის რედაქტირებისთვის, რომელიც შეიცავს textarea-ს და ღილაკებს
                var editForm = `
                    <form class="edit-form">
                        <textarea name="comment_text">${currentText}</textarea>
                        <button type="submit" class="save-edit">Save</button>
                        <button type="button" class="cancel-edit">Cancel</button>
                    </form>
                `;
                // მალავს ორიგინალურ კომენტარის ტექსტს (p თეგი)
                commentDiv.find('p:first').hide();
                // ამატებს რედაქტირების ფორმას comment div-ში
                commentDiv.append(editForm);
                // ამატებს მოვლენის მსმენელს "Save" ღილაკზე
                $('.save-edit').on('click', function(e) {
                    // ხელს უშლის ფორმის ნაგულისხმევ გაგზავნას
                    e.preventDefault();
                    // იღებს textarea-დან ახალ კომენტარის ტექსტს
                    var newText = $(this).closest('.edit-form').find('textarea').val();
                    // აგზავნის AJAX მოთხოვნას edit-comment.php-ზე POST მეთოდით
                    $.ajax({
                        // URL edit-comment.php-ზე, სადაც ხდება კომენტარის განახლება
                        url: './crud/edit-comment.php',
                        // მოთხოვნის ტიპი (POST)
                        type: 'POST',
                        // გაგზავნის comment_id-სა და ახალ ტექსტს
                        data: { comment_id: commentId, comment_text: newText },
                        // თუ მოთხოვნა წარმატებულია, ამოწმებს პასუხს
                        success: function(response) {
                            // თუ პასუხი შეიცავს status = 'success', განაახლებს კომენტარის ტექსტს და ხსნის ფორმას
                            if (response.status === 'success') {
                                commentDiv.find('p:first').text(commentDiv.find('strong').text() + ': ' + newText).show();
                                commentDiv.find('.edit-form').remove();
                            // თუ პასუხი არ არის წარმატებული, აჩვენებს შეცდომის შეტყობინებას
                            } else {
                                alert(response.message || 'Error updating comment.');
                            }
                        },
                        // თუ AJAX მოთხოვნა ვერ შესრულდა, აჩვენებს შეცდომის შეტყობინებას
                        error: function() {
                            alert('An error occurred.');
                        }
                    });
                });
                // ამატებს მოვლენის მსმენელს "Cancel" ღილაკზე
                $('.cancel-edit').on('click', function() {
                    // აჩვენებს ორიგინალურ კომენტარის ტექსტს (p თეგი)
                    commentDiv.find('p:first').show();
                    // შლის რედაქტირების ფორმას
                    commentDiv.find('.edit-form').remove();
                });
            });
        });
    </script>
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
    <div class="photo-detail-container">
        <div class="photo-section">
            <!-- ფოტოს გამოსახულება, src-ში მითითებულია image_path, alt-ში $customTitle, htmlspecialchars გამოიყენება უსაფრთხოებისთვის -->
            <img src="<?= htmlspecialchars($photo['image_path']) ?>" alt="<?= htmlspecialchars($customTitle) ?>">
            <!-- div, რომელიც შეიცავს ფოტოს სათაურს, აღწერას, კატეგორიასა და ატვირთვის თარიღს -->
            <div class="detail-content">
                <!-- ფოტოს სათაური, htmlspecialchars გამოიყენება უსაფრთხოებისთვის -->
                <h2><?= htmlspecialchars($customTitle) ?></h2>
                <!-- ფოტოს აღწერა, htmlspecialchars გამოიყენება უსაფრთხოებისთვის -->
                <p><?= htmlspecialchars($customDescription) ?></p>
                <!-- ფოტოს კატეგორიის სახელი, htmlspecialchars გამოიყენება უსაფრთხოებისთვის -->
                <p>Category: <?= htmlspecialchars($photo['category_name']) ?></p>
                <!-- ფოტოს ატვირთვის თარიღი, htmlspecialchars გამოიყენება უსაფრთხოებისთვის -->
                <p>Uploaded on: <?= htmlspecialchars($photo['created_at']) ?></p>
            </div>
        </div>
        <div class="comments-section">
            <!-- თუ მომხმარებელი შესულია, აჩვენებს კომენტარის დამატების ფორმას -->
            <?php if ($loggedIn): ?>
                <!-- ფორმა კომენტარის დასამატებლად, რომელიც POST მეთოდით გაგზავნის მონაცემებს add-comment.php-ზე -->
                <form method="POST" action="./crud/add-comment.php">
                    <!-- textarea კომენტარის ტექსტის შესაყვანად, required ატრიბუტი მოითხოვს, რომ ველი არ იყოს ცარიელი -->
                    <textarea name="comment" required placeholder="Write your comment..."></textarea>
                    <!-- ფარული input, რომელიც გადასცემს $photo_id-ს add-comment.php-ზე -->
                    <input type="hidden" name="photo_id" value="<?= htmlspecialchars($photo_id) ?>">
                    <!-- ღილაკი ფორმის გასაგზავნად -->
                    <button type="submit">Submit</button>
                <!-- ფორმის დასასრული -->
                </form>
            <!-- თუ მომხმარებელი არ არის შესული, აჩვენებს შეტყობინებას შესვლის მოთხოვნით -->
            <?php else: ?>
                <!-- შეტყობინება, რომელიც მოუწოდებს მომხმარებელს შესვლას კომენტარის დასამატებლად -->
                <p>Please <a href="auth/login.php">log in</a> to add a comment.</p>
            <!-- if პირობის დასასრული -->
            <?php endif; ?>
            <!-- სათაური კომენტარების სექციისთვის -->
            <h3>Comments</h3>
            <!-- ამოწმებს, აქვს თუ არა კომენტარების SQL მოთხოვნის შედეგს ($comments_result) მონაცემები -->
            <?php if (mysqli_num_rows($comments_result) > 0): ?>
                <!-- ციკლი, რომელიც გადის თითოეულ კომენტარზე SQL შედეგიდან, ინახავს მონაცემებს $comment ცვლადში -->
                <?php while ($comment = mysqli_fetch_assoc($comments_result)): ?>
                    <!-- div ელემენტი, რომელიც შეიცავს ცალკეულ კომენტარს -->
                    <div class="comment">
                        <!-- კომენტარის ტექსტი, რომელიც შეიცავს მომხმარებლის სახელს (strong თეგში) და კომენტარის ტექსტს, htmlspecialchars გამოიყენება უსაფრთხოებისთვის -->
                        <p><strong><?= htmlspecialchars($comment['username']) ?></strong>: <?= htmlspecialchars($comment['comment_text']) ?></p>
                        <!-- კომენტარის გამოქვეყნების თარიღი, htmlspecialchars გამოიყენება უსაფრთხოებისთვის -->
                        <p>Posted on: <?= htmlspecialchars($comment['created_at']) ?></p>
                        <!-- თუ მომხმარებელი ადმინისტრატორია, აჩვენებს წაშლისა და რედაქტირების ღილაკებს -->
                        <?php if ($isAdmin): ?>
                            <!-- ფორმა კომენტარის წასაშლელად, POST მეთოდით გაგზავნის მონაცემებს delete-comment.php-ზე -->
                            <form method="POST" action="./crud/delete-comment.php" style="display: inline;">
                                <!-- ფარული input, რომელიც გადასცემს კომენტარის ID-ს -->
                                <input type="hidden" name="comment_id" value="<?= htmlspecialchars($comment['id']) ?>">
                                <!-- ღილაკი კომენტარის წასაშლელად, onclick="return false;" ხელს უშლის ნაგულისხმევ გაგზავნას, რადგან JavaScript აიღებს კონტროლს -->
                                <button type="submit" class="delete-comment" onclick="return false;">Delete</button>
                            <!-- ფორმის დასასრული -->
                            </form>
                            <!-- ბმული კომენტარის რედაქტირებისთვის, data-comment-id ინახავს კომენტარის ID-ს JavaScript-ისთვის -->
                            <a href="#" class="edit-comment" data-comment-id="<?= htmlspecialchars($comment['id']) ?>">Edit</a>
                        <!-- if პირობის დასასრული -->
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <!-- შეტყობინება, თუ ფოტოსთვის კომენტარები არ არსებობს -->
                <p>No comments yet.</p>
            <?php endif; ?>
        </div>
    </div>
    <footer>
        <!-- აჩვენებს მიმდინარე წელს (date("Y")) და საავტორო შეტყობინებას -->
        <?php echo date("Y"); ?> Photo Gallery — Made with love by Keto
    </footer>
</body>
</html>
<?php
    // ხურავს ფოტოს მოთხოვნის prepared statement-ს, რათა გათავისუფლდეს რესურსები
    mysqli_stmt_close($stmt);
    // ხურავს კომენტარების მოთხოვნის prepared statement-ს, რათა გათავისუფლდეს რესურსები
    mysqli_stmt_close($comments_stmt);
    // ხურავს მონაცემთა ბაზასთან კავშირს ($conn), რათა გათავისუფლდეს რესურსები
    mysqli_close($conn);
?>