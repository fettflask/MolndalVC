<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$cookiepath = "/tmp/cookies.txt";
$baseurl = 'http://193.93.250.83:8080/';

// Logga in till API:t
try {
    $ch = curl_init($baseurl . 'api/method/login');
} catch (Exception $e) {
    echo 'Caught exception: ', $e->getMessage(), "\n";
    exit;
}

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"usr":"a23jaced@student.his.se", "pwd":"lmaokraftwerkvem?"}');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath); 
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath); 

$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch);
    exit;
}
curl_close($ch);

function fetchData($url, $cookiepath) {
    try {
        $ch = curl_init($url);
    } catch (Exception $e) {
        echo 'Caught exception: ', $e->getMessage(), "\n";
        exit;
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
        exit;
    }
    curl_close($ch);

    return $response; // Returnera rådata istället för att avkoda här
}

// Hämta rådata
$patientsRaw = fetchData('http://193.93.250.83:8080/api/resource/Patient?limit_page_length=None', $cookiepath);
$practitionersRaw = fetchData('http://193.93.250.83:8080/api/resource/Practitioner%20Schedule?limit_page_length=100', $cookiepath);

// Avkoda JSON-data utanför funktionen
$patientsData = json_decode($patientsRaw, true);
$practitionersData = json_decode($practitionersRaw, true);

// Kontrollera JSON-avkodning
if (json_last_error() !== JSON_ERROR_NONE || empty($patientsData['data']) || empty($practitionersData['data'])) {
    echo 'Error decoding JSON or no data received.';
    exit;
}

// Extrahera datafält
$patients = $patientsData['data'];
$practitioners = $practitionersData['data'];

?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dropdown med patienter och läkare</title>
</head>
<body>
    <h1>Välj Patient och Läkare</h1>

    <form action="Bokatid2.php" method="POST">
    
    <label for="patientDropdown">Välj en patient:</label>
    <select id="patientDropdown" name="selectedPatient">

        <?php
        foreach ($patients as $patient) {
            $name = htmlspecialchars($patient['name']);
            echo "<option value=\"$name\">$name</option>";
        }
        ?>
    </select>

    <label for="practitionerDropdown">Välj en läkare:</label>
    <select id="practitionerDropdown" name="selectedPractitioner">
        <?php
        foreach ($practitioners as $practitioner) {
            $name = htmlspecialchars($practitioner['name']);
            echo "<option value=\"$name\">$name</option>";
        }
        ?>
    </select>

    <button type="submit">Skicka val</button>
</form>

</body>
</html>
