<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'Funktioner/funktioner.php';

$cookiepath = "/tmp/cookies.txt";
$baseurl = 'http://193.93.250.83:8080/';

// Kontrollera att användarnamn finns i sessionen
if (!isset($_SESSION["namn"])) {
    echo "Inget användarnamn hittades i sessionen.";
    exit;
}

$anvandarnamn = $_SESSION["namn"];

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

// Hämta data för läkarscheman
$practitionersRaw = fetchData('http://193.93.250.83:8080/api/resource/Practitioner%20Schedule?filters=%5B%5B%22name%22%2C%22like%22%2C%22%25%28G6%29%25%22%5D%5D&limit_page_length=None', $cookiepath);

// Avkoda JSON-data för läkarscheman
$practitionersData = json_decode($practitionersRaw, true);

// Kontrollera JSON-avkodning
if (json_last_error() !== JSON_ERROR_NONE || empty($practitionersData['data'])) {
    echo 'Error decoding JSON or no data received.';
    exit;
}

// Extrahera datafält för läkare
$practitioners = $practitionersData['data'];

?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../IMG/favicon.png">
    <link rel="stylesheet" href="../Stylesheets/headerStyle.css">
    <link rel="stylesheet" href="../Stylesheets/valStyle.css">
    <link rel="stylesheet" href="../Stylesheets/footerStyle.css">
    <title>Boka tid</title>
</head>
<body>
    <?php echoHead() ?>

    <h1>Boka en tid för <?php echo htmlspecialchars($anvandarnamn); ?></h1>
    <div id="formMaster">
        <div id="centerForm">
            <form action="Bokatid2.php" method="POST">
                <!-- Använd sessionens användarnamn som patient -->
                <input type="hidden" name="selectedPatient" value="<?php echo htmlspecialchars($anvandarnamn); ?>">

                <div id="docSelect">
                    <label for="practitionerDropdown">Välj en läkare:</label>
                    <select id="practitionerDropdown" name="selectedPractitioner">
                        <?php
                        foreach ($practitioners as $practitioner) {
                            $name = htmlspecialchars($practitioner['name']);
                            echo "<option value=\"$name\">$name</option>";
                        }
                        ?>
                    </select>
                </div>
                <input type="submit" id="docSub" value='Boka'>
            </form>
        </div>
    </div>

    <?php echoFooter() ?>

</body>
</html>
