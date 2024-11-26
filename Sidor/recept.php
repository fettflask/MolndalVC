<?php
    session_start();
    include 'Funktioner/funktioner.php';
    if(!isset($_SESSION["namn"])){
        header("Location: patientLogin.php");
        die();
    }

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

        if (curl_errno($ch)) {
            // Get the error message from cURL
            echo 'cURL error: ' . curl_error($ch);
        } else {
            // Get the HTTP status code
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($http_code != 200) {
                echo "Unexpected HTTP status code: $http_code\n";
            }
        }


        $response = json_decode($response,true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "JSON decode error: " . json_last_error_msg() . "<br>";
        }
        
        return $response;
    }
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
    }

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

    if(isset($_POST["medicin"])){
        if(sendForm()){
            $_POST["request"] = "REDAN REQUESTAT";
        }
    }
?>

<!DOCTYPE html>
<html lang="en"></html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../IMG/favicon.png">
    <link rel="stylesheet" href="../Stylesheets/indexStyle.css">
    <link rel="stylesheet" href="../Stylesheets/headerStyle.css">
    <title>Mölndals Vårdcentral</title>
</head>
<body>
    <?php echoHead(); ?>

    <div>
        <p>
            Välj ur listan nedan vilken medicin du vill begära nytt recept på
        </p>
    </div>

    <div>
        <h3>Beställ recept</h3>
        <form method="POST" action="recept.php">
            <table>
                <?php
                    if(isset($_POST["medicin"])){
                        if(isset($_POST["request"]) && $_POST["request"] == "REDAN REQUESTAT"){
                            echo "Du har redan en aktiv beställning på " . $_POST["medicin"];
                        }
                        else{
                            echo "Du har nu beställt " . $_POST["medicin"];
                        }
                    }
                ?>
                <tr>
                    <td>
                        
                        <?php
                            curlSetup();
                            $name = str_replace(" ", "%20", $_SESSION["namn"]);

                            //Hämtar medicindata både från "Medication"-fält i "Medical history" i Patient, och från recept som lagts i samband med kundmöten
                            $medicineFromPatientPage = curlGetData('api/resource/Patient?filters={%22name%22:%22'. $name . '%22}&fields=[%22medication%22]');
                            $MedicineFromMedRequest = curlGetData('api/resource/Medication%20Request?limit_page_length=None&filters={"patient":"'. $name .'"}');
                            
                            //Skapar en array av datan från "Medication"-fält i "Medical history" i Patient samt separerar olika rader med explode
                            $mediciner = [];
                            foreach($medicineFromPatientPage as $row){
                                foreach($row as $row2){
                                    if($row2 && isset($row2["medication"]) && is_string($row2["medication"])){
                                        $mediciner = explode("\n", $row2["medication"]);
                                    }
                                    
                                }
                            }
                            
                            //Sparar all data från medication request i 2-dimentionell array för att kunna visa patienten medicinens namn i dropdown men skicka koden till app
                            $mediciner2 = [];
                            foreach($MedicineFromMedRequest as $row){
                                foreach($row as $row2){
                                    $row2Name = curlGetData('api/resource/Medication%20Request/' . $row2["name"]);
                                    foreach($row2Name as $row3){
                                        if(isset($row3["medication"])){
                                            $nyMedicin = array("medName" => $row3["medication"], "medCode" => $row3["medication_item"]);
                                        }else{
                                            $nyMedicin = array("medName" => $row3["medication_item"], "medCode" => $row3["medication_item"]);
                                        }
                                        array_push($mediciner2, $nyMedicin); 
                                    }   
                                }
                            }
                        

                            if(sizeof($mediciner) > 0 || sizeof($mediciner2) > 0){
                                $printList = [];
                                
                                if(sizeof($mediciner) > 0 && sizeof($mediciner2) > 0){
                                    //Om data hämtats från bägge källor slås printlisten ihop här
                                    foreach($mediciner2 as $row){
                                        $nyMedicin = array("medName" => $row["medName"], "medCode" => $row["medCode"]);
                                        array_push($printList, $nyMedicin);
                                    }
                                    foreach($mediciner as $row){
                                        $nyMedicin = array("medName" => $row, "medCode" => $row);
                                        array_push($printList, $nyMedicin);
                                    }
                                    $printList = array_map("unserialize", array_unique(array_map("serialize", $printList)));
                                }
                                else if(sizeof($mediciner) > 0 && !sizeof($mediciner2) > 0){
                                    //Om data bara hämtats från patient-doctype omvandlas arrayen till 2-dimensionell
                                    foreach($mediciner as $row){
                                        $nyMedicin = array("medName" => $row, "medCode" => $row);
                                        array_push($printList, $nyMedicin);
                                    }
                                } else{$printList = $mediciner2;}

                                echo '
                                Mediciner:
                                </td>
                                <td>
                                    <select name="medicin" required title="Välj från listan">
                                    <option selected hidden disabled>Välj Medicin</option>
                                    ';
                                foreach($printList as $medicin){
                                    //Value sätts till medCode då data hämtat från medReq har olika namn/kod och applikationen vill ha kod för ny medReq
                                    echo '<option value="' . $medicin["medCode"] . '">' . $medicin["medName"] . '</option>';
                                }
                                echo '</select>';
                                echo '</td></tr>';
                                echo"<tr><td><input type='submit' value='Skicka begäran'>";
                            }
                            else{
                                echo '<p>Vi hittade inga mediciner du har blivit utskriven tidigare. Fyll i </p> <a href="">kontaktformuläret</a><p> för vidare rådgivning</p>';
                            } 
                        ?>
                    </td>
                </tr>  
            </table> 
        </form>
    </div>
    

</body>
</html>
