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
    <link rel="stylesheet" href="../Stylesheets/resultatStyle.css">
    <title>Provsvar</title>
</head>
<body>
    <?php echoHead(); ?>   

    <main>
    <?php
            curlSetup();
            $labTest = getLabTester();

            if (isset($labTest['data']) && !empty($labTest['data'])) {
                echo '<div class="provMasterElement">';
                foreach ($labTest['data'] as $lab) {
                    echo '<details class="provElement">';
                    echo '<summary class="mainSummary">Prov: ' . htmlspecialchars($lab['lab_test_name'] ?? 'N/A') . '<br>Datum: ' . htmlspecialchars($lab['date'] ?? 'N/A') . '</summary>';

                    echo '<p>Prov: ' . htmlspecialchars($lab['name'] ?? 'N/A') . '</p>';
                    echo '<p>Ansvarig läkare: ' . htmlspecialchars($lab['practitioner_name'] ?? 'N/A') . '</p>';
                    echo '<p>Förväntad svarsdag: ' . htmlspecialchars($lab['expected_result_date'] ?? 'N/A') . '</p>';
                    echo '<hr>';

                    if (isset($lab['name'])) {
                        $labDetails = getLabResultat($lab);
                        
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
                                echo '<summary class="mainSummary">Prov resultat</summary>';
                                if (isset($labDetails['data']['normal_test_items']) && is_array($labDetails['data']['normal_test_items'])) {
                                        foreach ($labDetails['data']['normal_test_items'] as $provSvar) {
                                            echo '<div><hr>';
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
                                        }
                                    echo '<hr><p>Kommentar:</p>';
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
                echo '<div class="provMasterElement">';
                    echo '<details class="provElement"><summary>Inga labbprov tillgängliga</summary>';
                        echo '<p>Inga labbprov hittades. Du har inte genomfört några prover eller de har inte blivit registrerade ännu. </p>';
                    echo '</details>';
                echo '</div>';
            }
            ?>
    </main>
    <footer>

    </footer>
</body>
</html>