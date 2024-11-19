<?php
    session_start();

    $pdo = new PDO("mysql:dbname=grupp6;host=localhost", "sqllab", "Hare#2022");
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    function curlSetup($domainSuffix){
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $cookiepath = "/tmp/cookies.txt";
        $baseurl= 'http://193.93.250.83:8080/';
        
        try {
            $ch = curl_init($baseurl . $domainSuffix);
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

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "JSON decode error: " . json_last_error_msg() . "<br>";
        }
        
        return $response;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <script type="text/javascript">
        window.onload = function() {
            document.getElementById('loading').style.display = 'block';
            setTimeout(function() {
                document.getElementById('loading').style.display = 'none';
                document.getElementById('content').style.display = 'block';
            }, 2000);
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
            if pnr in api-lista: login --Done
                if pnr INTE i databas 
                    lägg till i databas
                Fixa en startskärm för patient
            else reroute tillbaka till patientLogin.php --Done
        */
        if (isset($_POST["pnr"])){
            $_SESSION["pnr"] = $_POST["pnr"];
            $_SESSION["timeout"] = 300;

            $patientPnr = curlSetup('api/resource/Patient?fields=["uid"]');
            
            $validCheck = false;
            
            foreach($patientPnr as $row){
            
                foreach($row as $row2){
                    
                    if($row2["uid"] == $_POST["pnr"]){
                        $validCheck = true;
                        break;
                    }
                }
            }

            if($validCheck){
                $pnrIDarabas = false;
                foreach($pdo ->query("select * from patientInlogg;") as $row) {
                    if($row["pnr"] == $_POST["pnr"]){
                        echo "bro what";
                        $pnrIDarabas = true;
                        break;
                    }
                }

                if($pnrIDarabas){
                    echo "<header>";
                    echo "<h1>Välkommen, " . $_POST["pnr"] . "</h1>";
                    echo "</header>";
                }
                else{
                    echo "<header>";
                    echo "<h1>Slow down, broski, du måste regga >:(</h1>";
                    echo "</header>";
                }
                
  
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