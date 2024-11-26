<?php
    session_start();
    include 'Funktioner/funktioner.php';
    if(!isset($_SESSION["namn"])){
        header("Location: patientLogin.php");
        die();
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
