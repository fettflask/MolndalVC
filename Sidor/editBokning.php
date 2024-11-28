<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// API-konfiguration
$cookiepath = "/tmp/cookies.txt";
$baseurl = 'http://193.93.250.83:8080/';

// Logga in till API:t
function apiLogin($baseurl, $cookiepath) {
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
}

apiLogin($baseurl, $cookiepath);

// Kontrollera om användaren är inloggad och om boknings-ID är tillgängligt
if (!isset($_SESSION["namn"]) || !isset($_GET['booking_id'])) {
    echo "Du är inte inloggad eller boknings-ID saknas.";
    exit;
}

$bookingId = $_GET['booking_id'];

// Funktion för att hämta detaljer för en specifik bokning
function getAppointmentDetails($baseurl, $cookiepath, $appointmentId) {
    $url = $baseurl . "api/resource/Patient%20Appointment/$appointmentId";
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

// Funktion för att uppdatera bokning
function updateAppointment($baseurl, $cookiepath, $appointmentId, $updatedData) {
    $url = $baseurl . "api/resource/Patient%20Appointment/$appointmentId";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updatedData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
        exit;
    }
    curl_close($ch);

    return json_decode($response, true);
}

// Hämta bokningsdetaljer
$appointmentDetails = getAppointmentDetails($baseurl, $cookiepath, $bookingId);

if (!$appointmentDetails) {
    echo "Bokningen kunde inte hittas.";
    exit;
}

// Funktion för att hämta lediga tider
function getAvailableTimeSlots($baseurl, $cookiepath, $practitioner) {
    $scheduleUrl = $baseurl . 'api/resource/Practitioner%20Schedule/' . rawurlencode($practitioner);
    $ch = curl_init($scheduleUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
    $scheduleResponse = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
        exit;
    }
    curl_close($ch);

    $scheduleData = json_decode($scheduleResponse, true);
    return $scheduleData['data']['time_slots'] ?? [];
}

$timeSlots = getAvailableTimeSlots($baseurl, $cookiepath, $appointmentDetails['practitioner']);

// Hantera formulärinmatning
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedData = [
        'appointment_date' => $_POST['appointment_date'],
        'appointment_time' => $_POST['selectedTimeSlot'], // Vald tidslucka
        'status' => $_POST['status']
    ];

    $updateResponse = updateAppointment($baseurl, $cookiepath, $bookingId, $updatedData);

    if (isset($updateResponse['data'])) {
        echo "Bokningen har uppdaterats!";
        header("Location: minaBokningar.php");
        exit;
    } else {
        echo "Ett fel inträffade vid uppdateringen.";
    }
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redigera Bokning</title>
</head>
<body>
    <h1>Redigera Bokning</h1>
    <form method="POST">
        <label for="appointment_date">Datum:</label>
        <input type="date" id="appointment_date" name="appointment_date" value="<?= htmlspecialchars($appointmentDetails['appointment_date']); ?>" required>
        <br>

        <h2>Lediga tider:</h2>
        <?php foreach ($timeSlots as $slot): ?>
            <?php $fromTime = htmlspecialchars($slot['from_time']); ?>
            <label>
                <input type="radio" name="selectedTimeSlot" value="<?= $fromTime ?>" required>
                <?= $fromTime ?>
            </label>
            <br>
        <?php endforeach; ?>

        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="Scheduled" <?= $appointmentDetails['status'] === 'Scheduled' ? 'selected' : ''; ?>>Scheduled</option>
            <option value="Completed" <?= $appointmentDetails['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
            <option value="Cancelled" <?= $appointmentDetails['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
        </select>
        <br><br>

        <button type="submit">Uppdatera Bokning</button>
    </form>
    <a href="minaBokningar.php">Tillbaka till Mina Bokningar</a>
</body>
</html>
