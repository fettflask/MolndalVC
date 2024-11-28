<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Sätt upp API-bas och cookies
$cookiepath = "/tmp/cookies.txt";
$baseurl = 'http://193.93.250.83:8080/';

// Logga in till API:t
$loginUrl = $baseurl . 'api/method/login';
$loginPayload = json_encode(['usr' => 'a23jaced@student.his.se', 'pwd' => 'lmaokraftwerkvem?']);

$ch = curl_init($loginUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $loginPayload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch);
    exit;
}
curl_close($ch);

// Hämta alla bokningar
function getAllAppointments($baseurl, $cookiepath) {
    $url = $baseurl . 'api/resource/Patient%20Appointment?limit_page_length=None';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
        exit;
    }
    curl_close($ch);

    $data = json_decode($response, true);
    return $data['data'] ?? [];
}

// Hämta detaljer för en specifik bokning
function getAppointmentDetails($baseurl, $cookiepath, $appointmentId) {
    $url = $baseurl . "api/resource/Patient%20Appointment/$appointmentId?limit_page_length=None";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
        exit;
    }
    curl_close($ch);

    $data = json_decode($response, true);
    return $data['data'] ?? null;
}

// Hämta användarens bokningar
$anvandarnamn = $_SESSION["namn"];
$allAppointments = getAllAppointments($baseurl, $cookiepath);

$userAppointments = [];

foreach ($allAppointments as $appointment) {
    $details = getAppointmentDetails($baseurl, $cookiepath, $appointment['name']);

    if (strpos($details['patient'], $anvandarnamn) === 0) {
        $userAppointments[] = $details;
    }
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Stylesheets/bokningarStyle.css">
    <title>Mina Bokningar</title>
</head>

<body>
    <h1>Välkommen, <?php echo htmlspecialchars($anvandarnamn); ?>!</h1>
    <h2>Dina Bokningar</h2>

    <?php if (!empty($userAppointments)): ?>
    <div id="bokningarMaster">    
        <?php foreach ($userAppointments as $booking): ?>
            <div id="bokningarElement">
                <div id="status">
                    <strong>Bokning:</strong> <?php echo htmlspecialchars($booking['title']); ?><br>
                    <strong>Datum:</strong> <?php echo htmlspecialchars($booking['appointment_date']); ?><br>
                    <strong>Tid:</strong> <?php echo htmlspecialchars($booking['appointment_time']); ?><br>
                    <strong>Status:</strong> <?php echo htmlspecialchars($booking['status']); ?><br>
                </div>
                <div id="buttonMaster">
                    <a class="buttonSlave" href="editBokning.php?booking_id=<?php echo $booking['name']; ?> ">
                            Omboka
                    </a>
                    <form method="POST" action="deleteBooking.php" style="display:inline;">
                        <input type="hidden" name="appointmentId" value="<?php echo htmlspecialchars($booking['name']); ?>">
                            <button type="submit" class="buttonSlave" onclick="return confirm('Är du säker på att du vill ta bort denna bokning?');">Avboka</button>                
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
        <p>Inga bokningar hittades för dig.</p>
    <?php endif; ?>
</body>
</html>

