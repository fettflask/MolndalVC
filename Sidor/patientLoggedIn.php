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
            $ch = curl_init('http://193.93.250.83:8080/' . $domainSuffix);
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
    function getGender(){
        $ch = curl_init('http://193.93.250.83:8080/api/resource/Gender');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_COOKIEFILE, "/tmp/cookies.txt");
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    function addPatient(){
        echo'
        <div>
            <p>
                Välkommen till att skapa ett konto som patient hos Mölndals Vårdcentral. 
                Fyll i alla fälten nedanför och verifiera med BankID.
            </p>
        </div>

        <div>
            <h3>Patient Inlogg</h3>
            <form method="POST" action="patientLoggedIn.php">
                <table>
                    <input type="text" name="pnr" hidden value="'. $_POST["pnr"] .'">
                    <tr>
                        <td>
                            Förnamn:
                        </td>
                        <td>
                            <input type="text" name="name" id="name" required pattern="[A-Za-zÅåÄäÖö]+(-[A-Za-zÅåÄäÖö]+)?" title="Endast bokstäver" placeholder="Förnamn">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Efternamn:
                        </td>
                        <td>
                            <input  type="text" name="lastname" id="lastname" required pattern="[A-Za-zÅåÄäÖö]+(-[A-Za-zÅåÄäÖö]+)?" title="Endast bokstäver" placeholder="Efternamn">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Kön:
                        </td>
                        <td>
                            <select name="sex" required title="Välj från listan">
                                <option selected hidden disabled>Välj kön</option>
        ';
        $genders = getGender();
        $genders = json_decode($genders, true);

        if (isset($genders['data']) && is_array($genders['data'])) {
            foreach ($genders['data'] as $gender) {
                echo '<option value="' . htmlspecialchars($gender['name']) . '">' . htmlspecialchars($gender['name']) . '</option>';
            }
        } else {
            echo '<option value="">No genders available</option>';
            if (isset($genders['message'])) {
                echo '<p>Error: ' . htmlspecialchars($genders['message']) . '</p>';
            } else {
                echo '<p>No gender data available in the response.</p>';
            }
        }
        echo '
                        </select>
                    </td>
                </tr>
            </table>
        ';
        echo"<input type='submit' value='Godkänn registrering via BankID'>";
        echo"</form>";
        echo"</div>";
    }

    function addPatientDB($pdo){
        
        $response = curlGetData("api/resource/Patient?filters={%22uid%22:%22". $_POST["pnr"] ."%22}");
        foreach($response as $row){
            foreach($row as $payload){
                $fullname = $payload["name"];
            }
        }
        
        $queryString = "insert into patient(pnr, fullNamn) values (:pnr, :namn);"; 
        $stmt = $pdo->prepare($queryString);
        $stmt->bindParam(':pnr', $_POST["pnr"]);
        $stmt->bindParam(':namn', $fullname);  
        
        try{ 
            $stmt->execute();                 
        }catch (PDOException $e){
            echo $e->getMessage(); 
        }
        $_SESSION["namn"] = $fullname;
        $_SESSION["pnr"] = $_POST["pnr"];
    }

    if(isset($_POST["name"])){
        curlSetup();  
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $cookiepath = "/tmp/cookies.txt";

        $ch = curl_init('http://193.93.250.83:8080/api/resource/Patient');

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"uid":"'.$_POST["pnr"].'","first_name":"'.$_POST["name"].'","last_name":"'.$_POST["lastname"].'","sex":"'.$_POST["sex"].'"}');
    
        

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
    <link rel="icon" type="image/x-icon" href="../IMG/favicon.png">
    <title>Mölndals Vårdcentral</title>
    <link rel="stylesheet" href="../Stylesheets/headerStyle.css">

    <script type="text/javascript">
        window.onload = function() {
            document.getElementById('loading').style.display = 'block';
            setTimeout(function() {
                document.getElementById('loading').style.display = 'none';
                document.getElementById('content').style.display = 'block';
                document.getElementById('header').style.display = 'flex';
            }, 2000);
            var shouldRunOnLoad = false;
        };   
    </script>
                            

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
        <a href="minaSidor.php">Mina sidor</a>
    </div>

    <div class="navbutton" id="buffer">
        <a href="">Sök vård</a>
    </div>

    <?php
        if(isset($_SESSION["namn"])){
            echo '<div class="navbutton">';
                 echo '<a href="sessionKill.php">Logga ut</a>';
            echo '</div>';
        }
    ?>

</header>
   
    <div id="loading" style="display: none; ">
        <img src="../IMG/bankID.png" alt="Öppna BankID">
    </div>
    <div id="content" style="display: none;">
    <?php
        /*
            if pnr in api-lista: 
                if pnr INTE i databas 
                    lägg till i databas --WIP
                Reroute till MinaSidor --Done
            else reroute tillbaka till patientLogin.php --Done
        */

        if (isset($_POST["pnr"])){
            $_SESSION["pnr"] = $_POST["pnr"];
            $_SESSION["timeout"] = 300;
            //Loggar in på webbuser
            curlSetup();

            //Hämtar alla patienters personnummer från ERP
            $patientPnr = curlGetData('api/resource/Patient?fields=["uid"]&limit_page_length=None');
            
            //Går igenom de hämtade personnumren och kollar om det inskrivna matchar med något
            $validCheck = false;
            foreach($patientPnr as $row){
                foreach($row as $row2){   
                    if($row2["uid"] == $_POST["pnr"]){
                        $validCheck = true;
                        break;
                    }
                }
            }

            //Om det angivna personnumret finns i ERP
            if($validCheck){
                
                //Går igenom databasen och kollar om det angivna PNR finns i DB
                $pnrIDarabas = false;                
                foreach($pdo ->query("select * from patient;") as $row) {
                    if($row["pnr"] == $_POST["pnr"]){
                        $pnrIDarabas = true;
                        $_SESSION["namn"] = $row["fullNamn"];
                        $_SESSION["pnr"] = $row["pnr"];
                        break;
                    }
                }

                //Om det fanns i databasen
                if($pnrIDarabas){
                    echo '<script>
                            window.setTimeout(function() {
                            window.location = "minaSidor.php";
                            }, 2000);
                        </script>';
                    
                }
                //Om inte i DB
                else{
                    addPatientDB($pdo);
                    echo '<script>
                            window.setTimeout(function() {
                            window.location = "minaSidor.php";
                            }, 2000);
                        </script>';
                }
                
  
            }
            //Om pnr Inte finns i ERP
            else{
                addPatient();
            }  
        }
        else{
            header("Location: patientLogin.php");
            $_SESSION["error"] = "Felaktikt personnummer";
            die();
        }
    
    ?>
    </div>

    <script>
        function capitalizeInput(event) {
            let input = event.target;
            let value = input.value.trim();

            value = value.replace(/[^a-zA-ZåäöÅÄÖ\s-]/g, '');

            input.value = value.replace(/(^|\s|-)([a-zåäö])/gu, function(match, p1, p2) {
                console.log("Matched Letter:", p2);
                switch (p2) {
                    case 'å': return p1 + 'Å';
                    case 'ä': return p1 + 'Ä';
                    case 'ö': return p1 + 'Ö';
                    default: return p1 + p2.toUpperCase();
                }
            });
        }

        document.getElementById('name').addEventListener('input', capitalizeInput);
        document.getElementById('lastname').addEventListener('input', capitalizeInput);
    </script>  
</body>
</html>