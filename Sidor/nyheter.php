<?php
    session_start();
    $_SESSION["timeout"] = 300;
    include 'Funktioner/funktioner.php';

    $pdo = new PDO("mysql:dbname=grupp6;host=localhost", "sqllab", "Hare#2022");
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../IMG/favicon.png">
    <link rel="stylesheet" href="../Stylesheets/headerStyle.css">
    <link rel="stylesheet" href="../Stylesheets/nyheter.css">
    <title>Nyheter</title>
</head>

<body>
    <?php echoHead(); ?>
    <main>
    <div id="healthCenterArticles">
        <h1>Nyheter</h1>
        <?php
            curlSetup();
            $bloggpost = getBloggPost();
            
            if (isset($bloggpost['data']) && !empty($bloggpost['data'])) {
                echo '<div class="articleList">';

                foreach ($bloggpost['data'] as $article) {
                    echo '<div class="articleCard">';
                    echo '<details>';
                    echo '<summary>' . $article["title"];
                    echo '<p>Publicerad: ' . $article["published_on"] . ' | Skriven av ' . $article["blogger"] . '</p></summary>';
                    echo '<p>' . $article["blog_intro"] . '</p>';
                    echo '<p>' . $article['content_html'] . '</p>';
                    echo '</details>';
                    echo '</div>';
                }
                echo '</div>';
            }
        ?>
    </div>
    </main>
    <footer>

    </footer>
</body>
</html>