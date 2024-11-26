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
    <title>Provsvar</title>
</head>
<body>
    <?php echoHead(); ?>
    <main>
        <?php
            curlSetup();
            $patientJournal = getPatientEncounters();
            $patientJournal = json_decode($patientJournal, true);
            

            if (isset($patientJournal['data']) && !empty($patientJournal['data'])) {
                echo '<div>';
                echo '<h2>Vårdmöten</h2>';
                foreach ($patientJournal['data'] as $journal) {
                    echo '<details>';
                    echo '<summary>Antäckning - ' . htmlspecialchars($journal['name'] ?? 'N/A') . ' med ' . htmlspecialchars($journal['practitioner_name'] ?? 'N/A') . ' <br>' . htmlspecialchars($journal['encounter_date'] ?? 'N/A') . '</summary>';

                    echo '<p>Ansvarig läkare: ' . htmlspecialchars($journal['practitioner_name'] ?? 'N/A') . '</p>';
                    echo '<p>Avdelning: ' . htmlspecialchars($journal['medical_department'] ?? 'N/A') . '</p>';
                    
                    echo '<p>Journal antäckning:<br>';
                    echo '' . htmlspecialchars($journal['encounter_comment'] ?? 'Finns ingen antäckning för mötet') . '</p>';
                    if (isset($journal['name'])) {
                        $JournalDetails = getPatientEncountersDetails($journal);
                        $JournalDetails = json_decode($JournalDetails, true);

                        $hasDiagnosis = false;
                        $hasProcedure = false;
                        $hasSymptoms = false;
                        $hasMedicine = false;

                        foreach ($JournalDetails['data']['diagnosis'] as $diagnosis) {
                            if (!empty($JournalDetails['data']['diagnosis'])) {
                                $hasDiagnosis = true;
                                break;
                            }
                        }

                        foreach ($JournalDetails['data']['procedure_prescription'] as $procedure) {
                            if (!empty($JournalDetails['data']['procedure_prescription'])) {
                                $hasProcedure = true;
                                break;
                            }
                        }

                        foreach ($JournalDetails['data']['symptoms'] as $symptoms) {
                            if (!empty($JournalDetails['data']['symptoms'])) {
                                $hasSymptoms = true;
                                break;
                            }
                        }

                        foreach ($JournalDetails['data']['drug_prescription'] as $symptoms) {
                            if (!empty($JournalDetails['data']['drug_prescription'])) {
                                $hasMedicine = true;
                                break;
                            }
                        }

                        if($hasDiagnosis){
                            if (isset($JournalDetails['data']['diagnosis']) && is_array($JournalDetails['data']['diagnosis'])) {
                                foreach ($JournalDetails['data']['diagnosis'] as $Detail) {
                                    echo '<p>';
                                    echo 'Diagnos: ' . htmlspecialchars($Detail['diagnosis'] ?? 'N/A') . '<br>';
                                    echo '</p>';                                    
                                }
                            }
                        }
                        
                        if($hasSymptoms){
                            echo '<details>';
                            echo '<summary>Symptom vid möte</summary>';
                            if (isset($JournalDetails['data']['symptoms']) && is_array($JournalDetails['data']['symptoms'])) {
                                echo '<div><ul>';
                                foreach ($JournalDetails['data']['symptoms'] as $Detail) {
                                        echo '<li>' . htmlspecialchars($Detail['complaint'] ?? 'N/A') . '</li>';
                                }
                                echo '</ul></div>';
                            }
                            echo '</details>';
                        }

                        if($hasProcedure){
                            echo '<details>';
                            echo '<summary>Genomförd procedur</summary>';
                            if (isset($JournalDetails['data']['procedure_prescription']) && is_array($JournalDetails['data']['procedure_prescription'])) {
                                    foreach ($JournalDetails['data']['procedure_prescription'] as $Detail) {
                                        echo '<div>';
                                            echo '<p>';
                                            echo 'Procedur: ' . htmlspecialchars($Detail['procedure'] ?? 'N/A') . '<br>';
                                            echo '</p>';
                                        echo '</div>';
                                        
                                    }
                                }
                            
                            echo '</details>';
                        }
                    }
                    
                    if($hasMedicine){
                        echo '<details>';
                        echo '<summary>Utskriven medicin</summary>';
                        if (isset($JournalDetails['data']['drug_prescription']) && is_array($JournalDetails['data']['drug_prescription'])) {
                            foreach ($JournalDetails['data']['drug_prescription'] as $Detail) {
                                echo '<div>';
                                    echo '<p>';
                                    echo 'Medicin: ' . htmlspecialchars($Detail['medication'] ?? 'N/A') . '<br>';
                                    echo 'Dos: ' . htmlspecialchars($Detail['dosage_form'] ?? 'N/A') . '<br>';
                                    echo 'Dos intervall: ' . htmlspecialchars($Detail['dosage'] ?? 'N/A') . '<br>';
                                    echo 'Ska tas i ' . htmlspecialchars($Detail['interval_uom'] ?? 'N/A') . '<br>';
                                    echo '</p>';
                                echo '</div>'; 
                            }
                        }
                        echo '</details>';
                    }
                    echo '</details>';
                }   
            }
            echo '</div>';

            $patientVital = getPatientVitals();
            $patientVital = json_decode($patientVital, true);

            if (isset($patientVital['data']) && !empty($patientVital['data'])) {
                echo '<div>';
                    echo '<h2>Vitalparametrar & mätningar</h2>';
                    foreach ($patientVital['data'] as $vital) {
                        echo '<details>';
                            echo '<summary>' . htmlspecialchars($vital['title'] ?? 'N/A') . '</summary>';                  
                            echo '<p>Kommentar: ' . htmlspecialchars($vital['vital_signs_note'] ?? 'N/A') . '</p>';
                            echo '<h4>Vitalparametrar</h4>';
                            echo '<p>';
                                echo 'Puls: ' . htmlspecialchars($vital['pulse'] ?? 'N/A') . '<br>';
                                echo 'Blodtryck: ' . htmlspecialchars($vital['bp'] ?? 'N/A') . '<br>';
                                echo 'Andetag/minut: ' . htmlspecialchars($vital['respiratory_rate'] ?? 'N/A') . '<br>';
                                echo 'Temperatur: ' . htmlspecialchars($vital['temperature'] ?? 'N/A') . 'C°';
                            echo '</p>';

                            echo '<h4>Mätning</h4>';
                            echo '<p>';
                                echo 'Längd: ' . htmlspecialchars($vital['height'] ?? 'N/A') . ' m (meter)<br>';
                                echo 'Vikt: ' . htmlspecialchars($vital['weight'] ?? 'N/A') . ' kg (kilogram)<br>';
                                echo 'Beräknad bmi: ' . htmlspecialchars($vital['bmi'] ?? 'N/A') . '<br>';
                            echo '</p>';

                        echo '</details>';
                    }
                    echo '</div>';
                }
        ?>
    </main>
    <footer>

    </footer>
</body>
</html>