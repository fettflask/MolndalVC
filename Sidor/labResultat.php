<?php
    session_start();
    $_SESSION["timeout"] = 300;

    $pdo = new PDO("mysql:dbname=grupp6;host=localhost", "sqllab", "Hare#2022");
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    function curlSetup(){
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $cookiepath = "/tmp/cookies.txt";

        try {
            $ch = curl_init('http://193.93.250.83:8080/api/method/login');
        } 
        catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

        curl_setopt($ch,CURLOPT_POST, true);

        curl_setopt($ch,CURLOPT_POSTFIELDS, '{"usr":"webb_user", "pwd":"Pangolin!24"}');

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept:
        application/json'));
        curl_setopt($ch,CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch,CURLOPT_COOKIEJAR, $cookiepath);
        curl_setopt($ch,CURLOPT_COOKIEFILE, $cookiepath);
        curl_setopt($ch,CURLOPT_TIMEOUT, $_SESSION["timeout"]);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
    }
    function getLabTester(){
        $name = str_replace(" ", "%20", $_SESSION["namn"]);;
        
        $ch = curl_init('http://193.93.250.83:8080/api/resource/Lab%20Test?filters={"patient":"'.$name.'"}&fields=["name","lab_test_name","date","expected_result_date","lab_test_comment","practitioner_name"]&order_by=date%20asc');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_COOKIEFILE, "/tmp/cookies.txt");

        $response = curl_exec($ch);

        return $response;
    }
    function getLabResultat($labTest){
        $ch = curl_init('http://193.93.250.83:8080/api/resource/Lab%20Test/'.$labTest["name"].'');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_COOKIEFILE, "/tmp/cookies.txt");

        $response = curl_exec($ch);

        return $response;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Stylesheets/headerStyle.css">
    <title>Provsvar</title>
</head>
<body>
    <header>
        <div id="companylogo">
            <a href="index.php">
                <img src="../IMG/MölndalLogo.png">
            </a>
        </div>

        <div id="topnav">
            <div class="navbox">
                <a href="">Nyheter</a>
            </div>

            <div class="navbox">
                <a href="">Sjukdomar & Besvär</a>
            </div>

            <div class="navbox">
                <a href="">Hälsoråd & Tips</a>
            </div>

            <div class="navbox">
                <a href="">Mer</a>
            </div>
        </div>

        <div class="navbutton" id="push">
            <a href="minaSidor.php">MINA SIDOR</a>
        </div>

        <div class="navbutton" id="buffer">
            <a href="">SÖK VÅRD</a>
        </div>
    </header>
    <main>
    <?php
            curlSetup();
            $labTest = getLabTester();
            $labTest = json_decode($labTest, true);

            if (isset($labTest['data']) && !empty($labTest['data'])) {
                echo '<div>';
                foreach ($labTest['data'] as $lab) {
                    echo '<details>';
                    echo '<summary>Prov: ' . htmlspecialchars($lab['lab_test_name'] ?? 'N/A') . '<br>Datum: ' . htmlspecialchars($lab['date'] ?? 'N/A') . '</summary>';

                    echo '<p>Prov: ' . htmlspecialchars($lab['name'] ?? 'N/A') . '</p>';
                    echo '<p>Ansvarig läkare: ' . htmlspecialchars($lab['practitioner_name'] ?? 'N/A') . '</p>';
                    echo '<p>Förväntad svarsdag: ' . htmlspecialchars($lab['expected_result_date'] ?? 'N/A') . '</p>';

                    if (isset($lab['name'])) {
                        $labDetails = getLabResultat($lab);
                        $labDetails = json_decode($labDetails, true);

                        if (isset($labDetails['data']['normal_test_items']) && is_array($labDetails['data']['normal_test_items'])) {
                            $hasResultValue = false;
                        
                            foreach ($labDetails['data']['normal_test_items'] as $provSvar) {
                                if (!empty($provSvar['result_value'])) {
                                    $hasResultValue = true;
                                    break;
                                }
                            }
                        
                            if ($hasResultValue) {
                                echo '<details>';
                                echo '<summary>Prov resultat</summary>';
                                if (isset($labDetails['data']['normal_test_items']) && is_array($labDetails['data']['normal_test_items'])) {
                                        foreach ($labDetails['data']['normal_test_items'] as $provSvar) {
                                            echo '<div>';
                                                echo '<p>';
                                                echo 'Prov ID: ' . htmlspecialchars($provSvar['lab_test_name'] ?? 'N/A') . '<br>';
                                                echo 'Resultat: ' . htmlspecialchars($provSvar['result_value'] ?? 'N/A') . '  ';
                                                if (!empty($provSvar['lab_test_uom'])){
                                                echo '<span>(' . htmlspecialchars($provSvar['lab_test_uom'] ?? 'N/A') . ')</span>';
                                                }

                                                $normalVärdeUppdelad = explode("\n", $provSvar['normal_range']);
                                                foreach ($normalVärdeUppdelad as $nVärde) {
                                                    echo '<p>Normalvärde: ' . htmlspecialchars($nVärde) . '</p>';
                                                }
                                                echo '</p>';
                                            echo '</div>';
                                            echo '<p>Kommentar:</p>';
                                        }
                                    }
                                }

                            echo '<p>' . htmlspecialchars($lab['lab_test_comment'] ?? 'Ingen kommentar från vårdgivare eller provresultat än, se förväntat svars datum') . '</p>';
                            echo '</details>';
                        }
                        } else {
                        echo '<p>Inga testresultat tillgängliga.</p>';
                        }

                        echo '</details>';
                    }
                echo '</div>';
            } else {
                echo '<div>';
                    echo '<details><summary>Inga labprov tillgängliga</summary>';
                        echo '<p>Inga labprov hittades.</p>';
                    echo '</details>';
                echo '</div>';
            }
            ?>
    </main>
    <footer>

    </footer>
</body>
</html>