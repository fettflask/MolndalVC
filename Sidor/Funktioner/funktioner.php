<?php
    session_start();
    /**
     * Skriver ut sidans header - används på alla sidor
     * @return void
     */
    function echoHead(){
        echo '
            <header>

                <div id="companylogo">
                    <a href="index.php">
                        <img src="../IMG/MölndalLogo.png">
                    </a>
                </div>
                    <div id="topnav">    

                        <div class="navbox">
                            <a href="nyheter.php">Nyheter</a>
                        </div>

                        <div class="navbox">
                            <a href="sjukdomarbesvär.php">Sjukdomar & Besvär</a>
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
                    </div>';

                    
                    if(isset($_SESSION["namn"])){
                        echo '<div class="navbutton">';
                                echo '<a href="sessionKill.php">Logga ut</a>';
                        echo '</div>';
                    }
        
        echo '</header>';
    }

    /**
     * Loggar in på ERP
     * 
     * Används i:
     *    patientLoggedIn.php
     *    recept.php
     *    skapaAnvändare.php
     *    patientJournal.php
     *    labResultat.php
     *    patientJournal.php
     * 
     * @return void
     */
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

    /**
     * Hämtar data från en angiven sida i API:et.
     * @param mixed $domainSuffix URL:ens ändelse vilken avgör vartifrån datan hämtas - specificeras vi påkallning av funktionen
     * @return mixed
     */
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

    /**
     * Hämtar kön från ERP samt skriver ut dem i option-taggar 
     * (Select behöver startas samt avslutas före respektive 
     * efter påkallning av funktion)
     * @return void
     */
    function getGender(){
        curlSetup();
        $ch = curl_init('http://193.93.250.83:8080/api/resource/Gender');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_COOKIEFILE, "/tmp/cookies.txt");
        $genders = curl_exec($ch);
        curl_close($ch);
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
    }

    /**
     * Kollar om aktuell patient ($_SESSION["namn"]) har en aktuell beställning på angiven medicin ($_POST["medicin"]). 
     * @return bool Return "true" om aktiv beställning finns, Return "False" om ingen aktiv bokning finns.
     */
    function checkRequests(){
        curlSetup();
        $name = str_replace(" ", "%20", $_SESSION["namn"]);
        $requests = curlGetData('api/resource/Medication%20Request?limit_page_length=None&filters={"patient":"'. $name .'"}');

        foreach($requests as $row){
            foreach($row as $row2){
                $row2Name = curlGetData('api/resource/Medication%20Request/' . $row2["name"]);
                foreach($row2Name as $row3){
                    if($row3["status"] = "active-Medication Request Status" || $row3["status"] = "draft-Medication Request Status" || $row3["status"] = "on-hold-Medication Request Status"){
                        if(isset($row3["medication"])){
                            if($_POST["medicin"] == $row3["medication"] || $_POST["medicin"] == $row3["medication_item"]){
                                return true;
                            }
                        }else{if($_POST["medicin"] == $row3["medication_item"]){
                            return true;
                        }}
                        
                    }
                }   
            }
        }
        return false;
    }

    /**
     * Postar formulärdatan till MedicationRequest i ERPNext
     * @return bool Return True om aktiv begäran av aktuell medicin redan finns innan post, return False om posten sker.
     */
    function sendForm(){
        curlSetup();  
        $med = addMed();
        $requestCheck = checkRequests();

        //Bryter begäran av recept då en ännu inte avslutad begäran för denna medicin redan ligger i systemet.
        if($requestCheck){ return true; }

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $cookiepath = "/tmp/cookies.txt";

        $ch = curl_init('http://193.93.250.83:8080/api/resource/Medication%20Request');

        curl_setopt($ch, CURLOPT_POST, true);
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{
            "company": "Mölndal VC(G6)",
            "status": "active-Medication Request Status",
            "medication_item": "' . $med . '",
            "patient": "' . $_SESSION["namn"] . '",
            "dosage_form": "TBD",
            "dosage": "TBD",
            "practitioner": "HLC-PRAC-2024-00046"
        }');
    
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept:
        application/json'));
        curl_setopt($ch,CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch,CURLOPT_COOKIEJAR, $cookiepath);
        curl_setopt($ch,CURLOPT_COOKIEFILE, $cookiepath);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

        curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'cURL error: ' . curl_error($ch);
        } else {
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($http_code != 200) {
                echo "Unexpected HTTP status code: $http_code\n";
            }
        }
        return false;
    }

    /**
     * Kollar om aktuell medicin redan finns i ERPNexts Item-DocType, och om inte lägger till den
     * @return mixed Returnerar _POST["medicin"] för att avsluta 
     */
    function addMed(){  
        $medication = curlGetData("api/resource/Item?limit_page_length=None");

        $validCheck = false;
        foreach($medication as $row){
            foreach($row as $row2){   
                if($row2["name"] == $_POST["medicin"]){
                    $validCheck = true;
                    break;
                }
            }
        }
        if($validCheck){
            return $_POST["medicin"];
        }

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $cookiepath = "/tmp/cookies.txt";

        $ch = curl_init('http://193.93.250.83:8080/api/resource/Item');

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '
                    {"item_code":"'. $_POST["medicin"] .'",
                    "item_group":"drug",
                    "stock_uom":"TBD",
                    "item_name":"'. $_POST["medicin"] . '"}
        ');
        
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept:
        application/json'));
        curl_setopt($ch,CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch,CURLOPT_COOKIEJAR, $cookiepath);
        curl_setopt($ch,CURLOPT_COOKIEFILE, $cookiepath);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

        curl_exec($ch);

        curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'cURL error: MEDICIN: ' . curl_error($ch);
        } else {
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            echo "HTTP Code MEDICIN: : $http_code\n";
        }

        return $_POST["medicin"];
    }

    /**
     * Hämtar labbresultat för angiven lab
     * @param mixed $labTest Labben som data hämtas för
     * @return mixed Returnerar data om param
     */
    function getLabResultat($labTest){
        $ch = curl_init('http://193.93.250.83:8080/api/resource/Lab%20Test/'.$labTest["name"].'');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_COOKIEFILE, "/tmp/cookies.txt");

        $response = curl_exec($ch);
        $response = json_decode($response, true);
        return $response;
    }

    /**
     * Hämtar alla labbtester för inloggad patient
     * @return mixed JSON-data för alla labbtester
     */
    function getLabTester(){
        $name = str_replace(" ", "%20", $_SESSION["namn"]);;
        
        $ch = curl_init('http://193.93.250.83:8080/api/resource/Lab%20Test?filters={"patient":"'.$name.'"}&fields=["name","lab_test_name","date","expected_result_date","lab_test_comment","practitioner_name"]&order_by=date%20asc');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_COOKIEFILE, "/tmp/cookies.txt");

        $response = curl_exec($ch);
        $response = json_decode($response, true);

        return $response;
    }
    
    /**
     * Printar HTML-formulär för adering av patient
     * @return void
     */
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
        getGender();

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

    /**
     * Lägger till ny patient i databasen
     * @param mixed $pdo - Anslutningen till databas
     * @return void
     */
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

    /**
     * Hämtar data från patientEncounter för inloggad patient.
     * @return mixed Returnerar den hämtade datan i JSON-frmat
     */
    function getPatientEncounters(){
        $name = str_replace(" ", "%20", $_SESSION["namn"]);
        
        $ch = curl_init('http://193.93.250.83:8080/api/resource/Patient%20Encounter?filters={"patient":"'.$name.'"}&fields=["name","title","encounter_date","encounter_time","practitioner_name","medical_department","encounter_comment"]&order_by=encounter_date%20asc');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_COOKIEFILE, "/tmp/cookies.txt");

        $response = curl_exec($ch);
        $response = json_decode($response, true);

        return $response;
    }

    /**
     * Hämtar patientjournalen från PatientEncounter för angiven patient
     * @param mixed $journal 
     * @return mixed Returnerar datan i JSON-format
     */
    function getPatientEncountersDetails($journal){
        $ch = curl_init('http://193.93.250.83:8080/api/resource/Patient%20Encounter/'.$journal["name"].'');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_COOKIEFILE, "/tmp/cookies.txt");

        $response = curl_exec($ch);
        $response = json_decode($response, true);

        return $response;
    }

    /**
     * Hämtar data om inloggad patients vitalsigns
     * @return mixed
     */
    function getPatientVitals(){
        $name = str_replace(" ", "%20", $_SESSION["namn"]);;
        
        $ch = curl_init('http://193.93.250.83:8080/api/resource/Vital%20Signs?filters={"patient":"'.$name.'"}&fields=["name","title","signs_date","bp","vital_signs_note","temperature","pulse","respiratory_rate","height","weight","bmi"]&order_by=signs_date%20asc');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_COOKIEFILE, "/tmp/cookies.txt");

        $response = curl_exec($ch);
        $response = json_decode($response, true);

        return $response;
    }
    /**
     * Hämtar blogg posts för nyhets sidan
     * retunerar data i json format
     */
    function getBloggPost(){        
        $ch = curl_init('http://193.93.250.83:8080/api/resource/Blog%20Post?fields=[%22name%22,%22published_on%22,%22blogger%22,%22content_html%22,%22title%22,%22published%22,%22blog_intro%22]&filters={%22published%22:%221%22,%22blogger%22:%22M%C3%B6lndal%20VC(G6)%22,%22blog_category%22:%22v%C3%A5rd-nyhet%22}&order_by=published_on%20asc');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_COOKIEFILE, "/tmp/cookies.txt");

        $response = curl_exec($ch);
        $response = json_decode($response, true);

        return $response;
    }
    /**
     * Hämtar blogg posts för sjukdoms delen för sjukdomar och besvär sidan
     * retunerar data i json format
     */
    function getBloggPostSjukdom(){        
        $ch = curl_init('http://193.93.250.83:8080/api/resource/Blog%20Post?fields=[%22name%22,%22published_on%22,%22blogger%22,%22content_html%22,%22blog_intro%22,%22title%22,%22published%22,%22blog_category%22]&filters={%22published%22:%221%22,%22blogger%22:%22M%C3%B6lndal%20VC(G6)%22,%22blog_category%22:%22sjukdomar%22}&order_by=published_on%20asc');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_COOKIEFILE, "/tmp/cookies.txt");

        $response = curl_exec($ch);
        $response = json_decode($response, true);

        return $response;
    }    
    /**
    * Hämtar blogg posts för besvär delen för sjukdomar och besvär sidan
    * retunerar data i json format
    */
    function getBloggPostBesvär(){        
        $ch = curl_init('http://193.93.250.83:8080/api/resource/Blog%20Post?fields=[%22name%22,%22published_on%22,%22blogger%22,%22content_html%22,%22blog_intro%22,%22title%22,%22published%22,%22blog_category%22]&filters={%22published%22:%221%22,%22blogger%22:%22M%C3%B6lndal%20VC(G6)%22,%22blog_category%22:%22besv%C3%A4r%22}&order_by=published_on%20asc');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_COOKIEFILE, "/tmp/cookies.txt");

        $response = curl_exec($ch);
        $response = json_decode($response, true);

        return $response;
    }
?>