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

        curl_setopt($ch,CURLOPT_POSTFIELDS, '{usr":"webb_user", "pwd":"Pangolin!24"}');

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
                    <tr>
                        <td>
                            Personnummer:
                        </td>
                        <td>
                        <input type="text" name="newpnr" id="pnr" pattern="[0-9]{8}-[0-9]{4}" required maxlength="13" placeholder="YYYYMMDD-XXXX" value="'. $_POST["pnr"]. '">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Förnamn:
                        </td>
                        <td>
                            <input type="text" name="name" id="name" required pattern="[A-Za-zÅåÄäÖö]+" title="Endast bokstäver" placeholder="Förnamn">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Efternamn:
                        </td>
                        <td>
                            <input  type="text" name="lastname" id="lastname" required pattern="[A-Za-zÅåÄäÖö]+" title="Endast bokstäver" placeholder="Efternamn">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Kön:
                        </td>
                        <td>
                            <select name="sex" required title="Välj från listan">
                                <option></option>
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
        echo "<input type='text' value=".$_POST["pnr"]." name='pnr' hidden>";
        echo"<input type='submit' value='Godkänn registrering via BankID'>";
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
                            
    <script>
        const pnrInput = document.getElementById("pnr");
        pnrInput.addEventListener("input", function () {
            let value = pnrInput.value.replace(/\D/g, '');
            if (value.length > 8) {
                value = value.slice(0, 8) + '-' + value.slice(8, 12);
            }
            pnrInput.value = value;
        });
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
                        $_SESSION["namn"] = $row["fullNamn"];
                        $_SESSION["pnr"] = $row["pnr"];
                        break;
                    }
                }

                if($pnrIDarabas){
                    echo '<script>
                            window.setTimeout(function() {
                            window.location = "minaSidor.php";
                            }, 2000);
                        </script>';
                    
                }
                else{
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

    <script>
        function capitalizeInput(event) {
            let input = event.target;
            let value = input.value.trim();

            // Remove all non-alphabetical characters except letters and spaces
            value = value.replace(/[^a-zA-ZåäöÅÄÖ\s]/g, '');

            // Capitalize the first letter of each word
            input.value = value.replace(/\b[a-zåäö]/gi, function(match) {
                return match.toUpperCase();
            });
        }

        document.getElementById('name').addEventListener('input', capitalizeInput);
        document.getElementById('lastname').addEventListener('input', capitalizeInput);
    </script>   
</body>
</html>