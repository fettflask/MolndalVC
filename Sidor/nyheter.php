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
    <?php     
    function getBloggPost(){        
        $ch = curl_init('http://193.93.250.83:8080/api/resource/Blog%20Post?fields=[%22name%22,%22published_on%22,%22blogger%22,%22content%22,%22blog_intro%22,%22content_html%22,%22title%22,%22published%22]&filters={"published":"1"}');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_COOKIEFILE, "/tmp/cookies.txt");

        $response = curl_exec($ch);
        $response = json_decode($response, true);

        return $response;
    }
    ?>
    <main>
    <div id="healthCenterArticles">
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