<?php
session_start();
require_once 'includes/db.php';

$photo_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$loggedIn = isset($_SESSION['user_id']);
$username = $loggedIn ? $_SESSION['username'] : '';

$isAdmin = $loggedIn && (
    (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') ||
    (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1)
);

$query = "SELECT photos.id, photos.image_path, photos.created_at, categories.name AS category_name 
          FROM photos 
          LEFT JOIN categories ON photos.category_id = categories.id 
          WHERE photos.id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $photo_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$photo = mysqli_fetch_assoc($result);

if (!$photo) {
    header("Location: index.php");
    exit();
}

$photoDetails = [
    1 => ['title' => 'Mount Everest', 'description' => 'Mount Everest, known officially and locally as Sagarmatha in Nepal or Qomolangma in Tibet, is Earth\'s highest mountain above sea level, located in the Mahalangur Himal sub-range of the Himalayas. The China–Nepal border runs across its summit point. Its elevation (snow height) of 8,848.86 m was most recently established in 2020 by the Chinese and Nepali authorities. Mount Everest attracts many climbers, including highly experienced mountaineers. There are two main climbing routes, one approaching the summit from the southeast in Nepal (known as the standard route) and the other from the north in Tibet. While not posing substantial technical climbing challenges on the standard route, Everest presents dangers such as altitude sickness, weather, and wind, as well as hazards from avalanches and the Khumbu Icefall. As of May 2024, 340 people have died on Everest. '],
    2 => ['title' => 'Ho Chi Minh City', 'description' => 'Ho Chi Minh City (HCMC), commonly known as Saigon, is the most populous city in Vietnam with a population of around 10 million in 2023.The city\'s geography is defined by rivers and canals, of which the largest is Saigon River. As a municipality, Ho Chi Minh City consists of 16 urban districts, five rural districts, and one municipal city (sub-city). As the largest financial centre in Vietnam, Ho Chi Minh City has the largest gross regional domestic product out of all Vietnam provinces and municipalities, contributing around a quarter of the country\'s total GDP. Ho Chi Minh City\'s metropolitan area is ASEAN\'s 5th largest economy, also the biggest outside an ASEAN country capital.'],
    3 => ['title' => 'Sossusvlei', 'description' => 'Sossusvlei (sometimes written Sossus Vlei) is a salt and clay pan surrounded by high red dunes, located in the southern part of the Namib Desert, in the Namib-Naukluft National Park of Namibia. The name "Sossusvlei" is often used in an extended meaning to refer to the surrounding area (including other neighbouring vleis such as Deadvlei and other high dunes). These landmarks are some of the major visitor attractions of Namibia. The name "Sossusvlei" is of mixed origin and roughly means "dead-end marsh". Vlei is the Afrikaans word for "marsh", while "sossus" is Nama for "no return" or "dead end". Sossusvlei owes this name to the fact that it is an endorheic drainage basin (i.e., a drainage basin without outflows) for the ephemeral Tsauchab River.'],
    4 => ['title' => 'Blue maomao', 'description' => 'The blue maomao (Scorpis violacea), also known as the violet sweep, blue sweep or hardbelly, is a species of marine ray-finned fish, a member of the subfamily Scorpidinae, part of the sea chub family Kyphosidae. It is native to the southwestern Pacific Ocean from Australia to New Zealand and the Kermadec Islands, where it can be found in inshore waters from the surface to depths of 30 m (98 ft). This fish can reach a length of 40 cm (16 in). It is commercially important and is also a popular game fish. The blue maomao has a laterally, compressed and relatively deep body with a noticeably forked tail. They have protrusible jaws, equipped with a number of rows of small, closely set teeth, which are used to capture larger zooplankton. The adults are deep blue dorsally and pale ventrally, at night they change colour to a mottled dark green. The juveniles are grey with a yellow anal fin. They can grow to a fork length of 45 centimetres (18 in).'],
    5 => ['title' => 'Amazon rainforest', 'description' => 'The Amazon rainforest, also called the Amazon jungle or Amazonia, is a moist broadleaf tropical rainforest in the Amazon biome that covers most of the Amazon basin of South America. This basin encompasses 7,000,000 km2 (2,700,000 sq mi), of which 6,000,000 km2 (2,300,000 sq mi) are covered by the rainforest. This region includes territory belonging to nine nations and 3,344 indigenous territories. The majority of the forest, 60%, is in Brazil, followed by Peru with 13%, Colombia with 10%, and with minor amounts in Bolivia, Ecuador, French Guiana, Guyana, Suriname, and Venezuela. Four nations have "Amazonas" as the name of one of their first-level administrative regions, and France uses the name "Guiana Amazonian Park" for French Guiana\'s protected rainforest area. The Amazon represents over half of the total area of remaining rainforests on Earth, and comprises the largest and most biodiverse tract of tropical rainforest in the world, with an estimated 390 billion individual trees in about 16,000 species.'],
    6 => ['title' => 'Aurora', 'description' => 'An aurora (pl. aurorae or auroras), also commonly known as the northern lights (aurora borealis) or southern lights (aurora australis), is a natural light display in Earth\'s sky, predominantly observed in high-latitude regions (around the Arctic and Antarctic). Auroras display dynamic patterns of radiant lights that appear as curtains, rays, spirals or dynamic flickers covering the entire sky. Auroras are the result of disturbances in the Earth\'s magnetosphere caused by enhanced speeds of solar wind from coronal holes and coronal mass ejections. These disturbances alter the trajectories of charged particles in the magnetospheric plasma. These particles, mainly electrons and protons, precipitate into the upper atmosphere (thermosphere/exosphere). The resulting ionization and excitation of atmospheric constituents emit light of varying color and complexity. The form of the aurora, occurring within bands around both polar regions, is also dependent on the amount of acceleration imparted to the precipitating particles.'],
    7 => ['title' => 'Colosseum', 'description' => 'The Colosseum is an elliptical amphitheatre in the centre of the city of Rome, Italy, just east of the Roman Forum. It is the largest ancient amphitheatre ever built, and is still the largest standing amphitheatre in the world, despite its age. Construction began under the Emperor Vespasian in 72 and was completed in AD 80 under his successor and heir, Titus. Further modifications were made during the reign of Domitian. The three emperors who were patrons of the work are known as the Flavian dynasty, and the amphitheatre was named the Flavian Amphitheatre by later classicists and archaeologists for its association with their family name (Flavius). The Colosseum is built of travertine limestone, tuff (volcanic rock), and brick-faced concrete. It could hold an estimated 50,000 to 80,000 spectators at various points in its history, having an average audience of some 65,000;'],
    8 => ['title' => 'Lion', 'description' => 'The lion (Panthera leo) is a large cat of the genus Panthera, native to Sub-Saharan Africa and India. It has a muscular, broad-chested body; a short, rounded head; round ears; and a dark, hairy tuft at the tip of its tail. It is sexually dimorphic; adult male lions are larger than females and have a prominent mane. It is a social species, forming groups called prides. A lion\'s pride consists of a few adult males, related females, and cubs. Groups of female lions usually hunt together, preying mostly on medium-sized and large ungulates. The lion is an apex and keystone predator. The lion inhabits grasslands, savannahs, and shrublands. It is usually more diurnal than other wild cats, but when persecuted, it adapts to being active at night and at twilight. During the Neolithic period, the lion ranged throughout Africa and Eurasia, from Southeast Europe to India, but it has been reduced to fragmented populations in sub-Saharan Africa and one population in western India.'],
];

$customTitle = isset($photoDetails[$photo_id]) ? $photoDetails[$photo_id]['title'] : 'Untitled Photo';
$customDescription = isset($photoDetails[$photo_id]) ? $photoDetails[$photo_id]['description'] : 'No description available.';

$comments_query = "SELECT comments.*, users.username 
                  FROM comments 
                  LEFT JOIN users ON comments.user_id = users.id 
                  WHERE comments.photo_id = ? 
                  ORDER BY comments.created_at DESC";
$comments_stmt = mysqli_prepare($conn, $comments_query);
mysqli_stmt_bind_param($comments_stmt, "i", $photo_id);
mysqli_stmt_execute($comments_stmt);
$comments_result = mysqli_stmt_get_result($comments_stmt);
?>

<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($customTitle) ?> - Photo Gallery</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.delete-comment').on('click', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this comment?')) {
                    var form = $(this).closest('form');
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: form.serialize(),
                        success: function(response) {
                            if (response === 'success') {
                                form.closest('.comment').remove();
                            } else {
                                alert('Error deleting comment.');
                            }
                        },
                        error: function() {
                            alert('An error occurred.');
                        }
                    });
                }
            });

            $('.comment').on('click', '.edit-comment', function(e) {
                e.preventDefault();
                var commentDiv = $(this).closest('.comment');
                var commentId = $(this).data('comment-id');
                var currentText = commentDiv.find('p:first').text().replace(commentDiv.find('strong').text() + ': ', '');

                var editForm = `
                    <form class="edit-form">
                        <textarea name="comment_text">${currentText}</textarea>
                        <button type="submit" class="save-edit">Save</button>
                        <button type="button" class="cancel-edit">Cancel</button>
                    </form>
                `;
                commentDiv.find('p:first').hide();
                commentDiv.append(editForm);

                $('.save-edit').on('click', function(e) {
                    e.preventDefault();
                    var newText = $(this).closest('.edit-form').find('textarea').val();
                    $.ajax({
                        url: './crud/edit-comment.php',
                        type: 'POST',
                        data: { comment_id: commentId, comment_text: newText },
                        success: function(response) {
                            if (response.status === 'success') {
                                commentDiv.find('p:first').text(commentDiv.find('strong').text() + ': ' + newText).show();
                                commentDiv.find('.edit-form').remove();
                            } else {
                                alert(response.message || 'Error updating comment.');
                            }
                        },
                        error: function() {
                            alert('An error occurred.');
                        }
                    });
                });

                $('.cancel-edit').on('click', function() {
                    commentDiv.find('p:first').show();
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
                <?php if ($loggedIn): ?>
                    <li><span>Hello, <?= htmlspecialchars($username) ?></span></li>
                    <li><a href="auth/contact.php">Contact</a></li>
                    <li><a href="auth/logout.php">Log out</a></li>
                <?php else: ?>
                    <li><a href="auth/register.php">Register</a></li>
                    <li><a href="auth/login.php">Log in</a></li>
                    <li><a href="auth/contact.php">Contact</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <div class="photo-detail-container">
        <div class="photo-section">
            <img src="<?= htmlspecialchars($photo['image_path']) ?>" alt="<?= htmlspecialchars($customTitle) ?>">
            <div class="detail-content">
                <h2><?= htmlspecialchars($customTitle) ?></h2>
                <p><?= htmlspecialchars($customDescription) ?></p>
                <p>Category: <?= htmlspecialchars($photo['category_name']) ?></p>
                <p>Uploaded on: <?= htmlspecialchars($photo['created_at']) ?></p>
            </div>
        </div>
        <div class="comments-section">
            <?php if ($loggedIn): ?>
                <form method="POST" action="./crud/add-comment.php">
                    <textarea name="comment" required placeholder="Write your comment..."></textarea>
                    <input type="hidden" name="photo_id" value="<?= htmlspecialchars($photo_id) ?>">
                    <button type="submit">Submit</button>
                </form>
            <?php else: ?>
                <p>Please <a href="auth/login.php">log in</a> to add a comment.</p>
            <?php endif; ?>
            <h3>Comments</h3>
            <?php if (mysqli_num_rows($comments_result) > 0): ?>
                <?php while ($comment = mysqli_fetch_assoc($comments_result)): ?>
                    <div class="comment">
                        <p><strong><?= htmlspecialchars($comment['username']) ?></strong>: <?= htmlspecialchars($comment['comment_text']) ?></p>
                        <p>Posted on: <?= htmlspecialchars($comment['created_at']) ?></p>
                        <?php if ($isAdmin): ?>
                            <form method="POST" action="./crud/delete-comment.php" style="display: inline;">
                                <input type="hidden" name="comment_id" value="<?= htmlspecialchars($comment['id']) ?>">
                                <button type="submit" class="delete-comment" onclick="return false;">Delete</button>
                            </form>
                            <a href="#" class="edit-comment" data-comment-id="<?= htmlspecialchars($comment['id']) ?>">Edit</a>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No comments yet.</p>
            <?php endif; ?>
        </div>
    </div>
    <footer>
        <?php echo date("Y"); ?> Photo Gallery — Made with love by Keto
    </footer>
</body>
</html>
<?php
    mysqli_stmt_close($stmt);
    mysqli_stmt_close($comments_stmt);
    mysqli_close($conn);
?>