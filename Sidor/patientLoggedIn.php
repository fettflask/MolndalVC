<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script type="text/javascript">
        // Wait for the DOM content to load
        window.onload = function() {
            // Show loading message
            document.getElementById('loading').style.display = 'block';

            // Use setTimeout to delay the action
            setTimeout(function() {
                // Hide loading message after 2 seconds
                document.getElementById('loading').style.display = 'none';
                // Show the rest of the content after 2 seconds
                document.getElementById('content').style.display = 'block';
            }, 2000); // 2000 milliseconds = 2 seconds
        };
    </script>
</head>
<body>
    <div id="loading" style="display: none; ">
        <img src="../IMG/bankID.png" alt="Öppna BankID">
    </div>
    <div id="content" style="display: none;">
    <?php
        /*
            if pnr in api-lista: login 
                if pnr INTE i databas 
                    lägg till i databas
                
            else reroute tillbaka till patientLogin.php
        */
        if (isset($_POST["pnr"])){
            $_SESSION["pnr"] = $_POST["pnr"];
            $_SESSION["timeout"] = 300; // (300=5min)

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

            // ANVÄNDER MASTERINLOGG - WEBBUSER
            curl_setopt($ch,CURLOPT_POSTFIELDS, '{"usr":"' . "a23jaced@student.his.se" . '", "pwd":"' . "lmaokraftwerkvem?" . '"}');

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

            try {
                $ch = curl_init($baseurl.'api/resource/Patient?fields=["uid"]');
            } 
            catch (Exception $e) {
                echo 'Caught exception: ', $e->getMessage(), "\n";
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept:
            application/json'));
            curl_setopt($ch,CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch,CURLOPT_COOKIEJAR, $cookiepath);
            curl_setopt($ch,CURLOPT_COOKIEFILE, $cookiepath);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $response = json_decode($response,true);
            
            
            $validCheck = false;
            foreach($response as $row){
                foreach($row as $row2){
                    if($row2["uid"] == $_POST["pnr"]){
                        $validCheck = true;
                    }
                }
            }

            if($validCheck){
                echo "<header>";
                echo "<h1>Välkommen, " . $_POST["pnr"] . "</h1>";
                echo "</header>";
                
            }
            else{
                header("Location: patientLogin.php");
                $_SESSION["error"] = "Felaktikt eller inaktivt personnummer";
                die();
                
            }  
    }
    else{
        header("Location: patientLogin.php");
        $_SESSION["error"] = "Felaktikt eller inaktivt personnummer";
        die();
    }
    ?>
    </div>
</body>
</html>