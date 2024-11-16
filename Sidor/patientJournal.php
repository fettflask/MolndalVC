<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Document</title>
</head>
<body>
    <header>
        <h1>Välkommer till din journal, Jonas Svenson</h1>
    </header>
    <main>

        <?php
        
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
            $cookiepath = "/tmp/cookies.txt";

            // här sätter ni er domän
            $baseurl= 'http://193.93.250.83:8080/';

                try {
                    $ch = curl_init($baseurl.'api/method/login');
                } 
                catch (Exception $e) {
                    echo 'Caught exception: ', $e->getMessage(), "\n";
                }

            curl_setopt($ch,CURLOPT_POST, true);

            // Här sätter ni era login-data
            curl_setopt($ch,CURLOPT_POSTFIELDS, '{"usr":"' . $_SESSION["anamn"] . '", "pwd":"' . $_SESSION["pword"] . '"}');

            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept:
            application/json'));
            curl_setopt($ch,CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch,CURLOPT_COOKIEJAR, $cookiepath);
            curl_setopt($ch,CURLOPT_COOKIEFILE, $cookiepath);
            curl_setopt($ch,CURLOPT_TIMEOUT, $_SESSION["timeout"]);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            $response = json_decode($response,true);
            $error_no = curl_errno($ch);
            $error = curl_error($ch);
            curl_close($ch);

            if(!empty($error_no)) {

                echo "<div style='background-color:red'>";
                echo '$error_no<br>';
                var_dump($error_no);
                echo "<hr>";
                echo '$error<br>';
                var_dump($error);
                echo "<hr>";
                echo "</div>";
            }

            echo "<div style='background-color:lightgray; border:1px solid black'>";
            echo '$response<br><pre>';
            echo print_r($response)."</pre><br>";
            echo "</div>";
        ?>
    </main>
</body>
</html>