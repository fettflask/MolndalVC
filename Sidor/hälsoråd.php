<?php
    session_start();
    $_SESSION["timeout"] = 300;
    include 'Funktioner/funktioner.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../IMG/favicon.png">
    <link rel="stylesheet" href="../Stylesheets/headerStyle.css">
    <link rel="stylesheet" href="../Stylesheets/hälsoråd.css">
    <link rel="stylesheet" href="../Stylesheets/footerStyle.css">
    <title>Nyheter</title>
</head>

<body>
    <?php echoHead(); 
        function getBloggPostHäldoråd(){        
            $ch = curl_init('http://193.93.250.83:8080/api/resource/Blog%20Post?fields=[%22name%22,%22published_on%22,%22blogger%22,%22content_html%22,%22title%22,%22published%22,%22blog_category%22]&filters={%22published%22:%221%22,%22blogger%22:%22M%C3%B6lndal%20VC(G6)%22,%22blog_category%22:%22h%C3%A4lsor%C3%A5d%22}&order_by=published_on%20asc');
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
            $bloggpostHäldoråd = getBloggPostHäldoråd();
            
            if (isset($bloggpostHäldoråd['data']) && !empty($bloggpostHäldoråd['data'])) {
                echo '<div class="articleList">';

                foreach ($bloggpostHäldoråd['data'] as $articleHäldoråd) {
                    echo '<div class="articleCard">';
                    echo '<h1>' . $articleHäldoråd["title"] . '</h1>';
                    echo '<div>' . $articleHäldoråd['content_html'] . '</div>';
                    echo '</div>';
                }
                echo '</div>';
            }
        ?>
    </div>
    </main>
    <?php echoFooter(); ?>
</body>
</html>