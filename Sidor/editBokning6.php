<?php
session_start();
include "Funktioner/funktioner.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$cookiepath = "/tmp/cookies.txt";
$baseurl = 'http://193.93.250.83:8080/';



// Logga in till API:t
curlSetup();

// Kontrollera parametrar
$bookingId = $_GET['booking_id'] ?? null;
$practitioner = $_GET['practitioner_name'] ?? null;

if (!$bookingId || !$practitioner) {
    echo "Saknar nödvändig data för att fortsätta.";
    exit;
}

// Hämta schema för vald läkare
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
$timeSlots = $scheduleData['data']['time_slots'] ?? [];
if (empty($timeSlots)) {
    echo 'Inga tidsluckor tillgängliga för vald läkare.';
    exit;
}

// Hämta bokningar för vald läkare
$appointmentsUrl = $baseurl . 'api/resource/Patient%20Appointment?limit_page_length=None';
$ch = curl_init($appointmentsUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
$appointmentsResponse = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch);
    exit;
}
curl_close($ch);

$appointmentsData = json_decode($appointmentsResponse, true);
$bookedAppointments = $appointmentsData['data'] ?? [];

// Filtrera bokningar för vald läkare
$bookedSlots = [];
foreach ($bookedAppointments as $appointment) {
    $detailsUrl = $baseurl . 'api/resource/Patient%20Appointment/' . rawurlencode($appointment['name']);
    $ch = curl_init($detailsUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
    $detailsResponse = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
        continue;
    }
    curl_close($ch);

    $appointmentDetails = json_decode($detailsResponse, true)['data'] ?? [];
    if ($appointmentDetails['practitioner_name'] === $practitioner || $appointmentDetails['practitioner'] === $practitioner) {
        $date = $appointmentDetails['appointment_date'];
        $time = $appointmentDetails['appointment_time'];
        if (!isset($bookedSlots[$date])) {
            $bookedSlots[$date] = [];
        }
        $bookedSlots[$date][] = $time;
    }
}

// Filtrera schematider mot bokade tider
$groupedSlots = [];
$today = new DateTime(); // Nuvarande datum och tid
    date_default_timezone_set('Europe/Stockholm');

    $groupedSlots = [];
    foreach ($timeSlots as $slot) {
        $day = $slot['day'];
        $fromTime = $slot['from_time'];
        $nextDateInfo = getNextDateForDay($day, $today);
        $date = $nextDateInfo['date'];

        $slotDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $date . ' ' . $fromTime);

        if ($date == $today->format('Y-m-d')) {

            if ($slotDateTime <= $today) {
                continue; 
            }
        }

        // Kontrollera om tiden är bokad
        if (!isset($bookedSlots[$date]) || !in_array($fromTime, $bookedSlots[$date])) {
            if (!isset($groupedSlots[$date])) {
                $groupedSlots[$date] = [
                    'day' => $nextDateInfo['day'],
                    'slots' => []
                ];
            }
            $groupedSlots[$date]['slots'][] = $slot;
        }
    }

    // Sortera datumen så att dagens kommer först
    uksort($groupedSlots, function ($a, $b) {
        return strtotime($a) - strtotime($b);
    });

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedDate = $_POST['selectedDate'] ?? null;
    $selectedTime = $_POST['selectedTimeSlot'] ?? null;

    if (!$selectedDate || !$selectedTime) {
        echo "Du måste välja ett datum och en tid.";
        exit;
    }

    // Skapa payload för att uppdatera bokningen
    $updatePayload = json_encode([
        'appointment_date' => $selectedDate,
        'appointment_time' => $selectedTime,
    ]);

    // Skicka PUT-förfrågan till API
    curlSetup();
    $updateUrl = $baseurl . 'api/resource/Patient%20Appointment/' . rawurlencode($bookingId);

    $ch = curl_init($updateUrl);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $updatePayload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
        exit;
    }
    curl_close($ch);

    $responseData = json_decode($response, true);

    if (!isset($responseData['data'])) {
        echo "Ombokningen misslyckades. Felmeddelande: " . ($responseData['message'] ?? 'Inget meddelande från API');
        var_dump('API Response:', $response);
        exit;
    }

    echo "Ombokningen lyckades!";

    if (isset($responseData['data'])) {
        // Omdirigera användaren efter lyckad uppdatering
        header("Location: http://193.93.250.83/wwwit-utv/Grupp%206/Sidor/bokningsHantering.php");
        exit;
    } else {
        echo "Misslyckades att uppdatera bokningen.";
    }
}

?>



<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Omboka tid</title>
    <script>
        function updateTimeSlots() {
            const selectedDate = document.getElementById('dateDropdown').value;
            const allSlots = document.querySelectorAll('.time-slots');
            allSlots.forEach(slot => {
                slot.style.display = slot.dataset.date === selectedDate ? 'block' : 'none';
            });
            document.getElementById('selectedDateInput').value = selectedDate;
        }
    </script>
</head>
<body>
    <h1>Omboka din tid</h1>
    <p>Läkare: <?= htmlspecialchars($practitioner) ?></p>

    <form method="POST">
        <label for="dateDropdown">Välj ett datum:</label>
        <select id="dateDropdown" name="dateDropdown" onchange="updateTimeSlots()">
            <option value="">-- Välj datum --</option>
            <?php foreach ($groupedSlots as $date => $info): ?>
                <option value="<?= htmlspecialchars($date) ?>">
                    <?= htmlspecialchars($info['day']) ?> (<?= htmlspecialchars($date) ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <?php foreach ($groupedSlots as $date => $info): ?>
            <div class="time-slots" data-date="<?= htmlspecialchars($date) ?>" style="display: none;">
                <h3>Tillgängliga tider för <?= htmlspecialchars($info['day']) ?> (<?= htmlspecialchars($date) ?>)</h3>
                <?php foreach ($info['slots'] as $slot): ?>
                    <label>
                        <input type="radio" name="selectedTimeSlot" value="<?= htmlspecialchars($slot['from_time']) ?>" required>
                        <?= htmlspecialchars($slot['from_time']) ?>
                    </label>
                    <br>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <input type="hidden" id="selectedDateInput" name="selectedDate" value="">
        <button type="submit">Uppdatera bokning</button>
    </form>
</body>
</html>