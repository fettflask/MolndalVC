<?php
    session_start();

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

        curl_setopt($ch,CURLOPT_POSTFIELDS, '{"usr":"a23jaced@student.his.se", "pwd":"lmaokraftwerkvem?"}');

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept:
        application/json'));
        curl_setopt($ch,CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch,CURLOPT_COOKIEJAR, $cookiepath);
        curl_setopt($ch,CURLOPT_COOKIEFILE, $cookiepath);
        curl_setopt($ch,CURLOPT_TIMEOUT, $_SESSION["timeout"]);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
    }
    function curlGetData($domainSuffix){
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $cookiepath = "/tmp/cookies.txt";
        
        try {
            $ch = curl_init('http://193.93.250.83:8080/' . $domainSuffix . "&limit_page_length=None");
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

    function addPatient(){
        echo"<div>";
        echo"<h3>Registrering</h3>";
        echo"<form method='POST' action='patientLoggedIn.php'>";
        echo"<table>";
        echo '<tr><td>Personnummer:</td><td><input type="text" name="newpnr" id="pnr" pattern="[0-9]{8}-[0-9]{4}" required maxlength="13"  value="'. $_POST["pnr"] .'"</td></tr>';
        echo '               
        <script>
            const pnrInput = document.getElementById("pnr");

            pnrInput.addEventListener("input", function () {
                let value = pnrInput.value.replace(/\D/g, "");

                if (value.length > 8) {
                    value = value.slice(0, 8) + "-" + value.slice(8, 12);
                }

                pnrInput.value = value;
            });
        </script>';
        echo"</td></tr>";

        echo"<tr><td>Förnamn:</td><td><input type='text' name='name' required autocapitalize='on'></td> </tr>";
        echo"<tr><td>Efternamn:</td><td><input type='text' name='lastname' required autocapitalize='on'></td></tr>";
        echo"<tr><td>Kön:</td><td><input type='text' name='sex' required></td></tr>";
        echo"</table>";
        echo "<input type='text' value=".$_POST["pnr"]." name='pnr' hidden>";
        echo"<input type='submit' value='Identifiera med BankID'>";
        echo"</form>";
        echo"</div>";
    }

    if(isset($_POST["newpnr"])){
        curlSetup();

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $cookiepath = "/tmp/cookies.txt";

        $ch = curl_init('http://193.93.250.83:8080/api/resource/Patient');

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"uid":"'.$_POST["newpnr"].'","first_name":"'.$_POST["name"].'","last_name":"'.$_POST["lastname"].'","sex":"'.$_POST["sex"].'"}');
    
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept:
        application/json'));
        curl_setopt($ch,CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch,CURLOPT_COOKIEJAR, $cookiepath);
        curl_setopt($ch,CURLOPT_COOKIEFILE, $cookiepath);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

        curl_exec($ch);
        $fullname = $_POST["name"] . " " . $_POST["lastname"];
        $queryString = "insert into patient(pnr, fullNamn) values (:pnr, :namn);"; 
        $stmt = $pdo->prepare($queryString);
        $stmt->bindParam(':pnr', $_POST["pnr"]);
        $stmt->bindParam(':namn', $fullname);  
        
        try{ 
            $stmt->execute();                  
        }catch (PDOException $e){
            echo $e->getMessage(); 
        }
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
            var shouldRunOnLoad = false;
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
            //Loggar in på webbuser
            curlSetup();

            $patientPnr = curlGetData('api/resource/Patient?fields=["uid"]');
            
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
                foreach($pdo ->query("select * from patient;") as $row) {
                    if($row["pnr"] == $_POST["pnr"]){
                        $pnrIDarabas = true;
                        $patientNamn = $row["fullNamn"];
                        break;
                    }
                }

                if($pnrIDarabas){
                    echo "<header>";
                    echo "<h1>Välkommen, " . $patientNamn . "</h1>";
                    echo "</header>";
                }
                else{
                    echo "<header>";
                    echo "<h1>Slow down, broski, du måste regga >:(</h1>";
                    echo "</header>";
                    addPatient();
                }
                
  
            }
            else{

                $regex = '#^[0-9]{8}-[0-9]{4}$#';
                if (preg_match($regex, $_POST["pnr"])) {
                    addPatient();
                } else {
                    header("Location: patientLogin.php");
                    $_SESSION["error"] = "Felaktikt personnummer";
                    die();
                   
                }
            }  
        }
        else{
            header("Location: patientLogin.php");
            $_SESSION["error"] = "Felaktikt personnummer";
            die();
        }
    
    ?>
    </div>
</body>
</html>