<?php
session_start();
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
curlSetup();

// Hämta data för läkarscheman
$practitionersData = curlGetData('api/resource/Practitioner%20Schedule?filters=%5B%5B%22name%22%2C%22like%22%2C%22%25%28G6%29%25%22%5D%5D&limit_page_length=None');

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
            <form action="Bokatid2Test2.php" method="POST">
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
