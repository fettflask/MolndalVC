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
    <link rel="stylesheet" href="../Stylesheets/sjukdom&besvär.css">
    <title>Nyheter</title>
</head>

<body>
    
    <?php echoHead(); 
    

    ?>
    <main>
    <div id="healthCenterArticles">

        <div class="articleList">
            <?php
                curlSetup();
                $bloggpostSjukdom = getBloggPostSjukdom();
                $bloggpostBesvär = getBloggPostBesvär();
                
                if (isset($bloggpostSjukdom['data']) && !empty($bloggpostSjukdom['data'])) {
                    echo '<div class="sectionSjukdomar">';
                    echo '<h2>Sjukdomar</h2>';
                    foreach ($bloggpostSjukdom['data'] as $articleSjukdom) {
                        echo '<div class="articleCard">';
                        echo '<details>';
                        echo '<summary>' . $articleSjukdom["title"];
                        echo '<p>Publicerad: ' . $articleSjukdom["published_on"] . ' | Skriven av ' . $articleSjukdom["blogger"] . '</p>';
                        echo '<p>' . $articleSjukdom["blog_intro"] . '</p><p>Vill du läsa mer klicka på artikeln</p></summary>';
                        echo '<p>' . $articleSjukdom['content_html'] . '</p>';
                        echo '</details>';
                        echo '</div>';
                    }
                    echo '</div>';
                }
                
                if (isset($bloggpostBesvär['data']) && !empty($bloggpostBesvär['data'])) {
                    echo '<div class="sectionBesvar">';
                    echo '<h2>Besvär</h2>';
                    foreach ($bloggpostBesvär['data'] as $articleBesvär) {
                        echo '<div class="articleCard">';
                        echo '<details>';
                        echo '<summary>' . $articleBesvär["title"];
                        echo '<p>Publicerad: ' . $articleBesvär["published_on"] . ' | Skriven av ' . $articleBesvär["blogger"] . '</p>';
                        echo '<p>' . $articleBesvär["blog_intro"] . '</p><p>Vill du läsa mer klicka på artikeln</p></summary>';
                        echo '<p>' . $articleBesvär['content_html'] . '</p>';
                        echo '</details>';
                        echo '</div>';
                    }
                    echo '</div>';
                }
            ?>
        </div>
    </div>

    </main>
    <footer>

    </footer>
</body>
</html>