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
$timeData = $_GET['appointment_time'] ?? null;
$DateData = $_GET['appointment_date'] ?? null;

$practitioner = trim($practitioner);


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
    if (trim($appointmentDetails['practitioner_name']) === $practitioner || trim($appointmentDetails['practitioner']) === $practitioner) {
        $date = $appointmentDetails['appointment_date'];
        $time = $appointmentDetails['appointment_time'];
    
        if (!isset($bookedSlots[$date])) {
            $bookedSlots[$date] = [];
        }
        $bookedSlots[$date][] = $time;
    }
    
    if (empty($appointmentDetails)) {
        echo "Tomt svar från detaljer-API för: ";
        var_dump($detailsUrl);
        continue;
    }
    if (!isset($appointmentDetails['practitioner_name']) || !isset($appointmentDetails['appointment_date']) || !isset($appointmentDetails['appointment_time'])) {
        echo "Saknade nycklar i detaljer-API: ";
        var_dump($appointmentDetails);
        continue;
    }
    
}

// Filtrera schematider mot bokade tider
$today = new DateTime();
date_default_timezone_set('Europe/Stockholm');

// Funktion för att hämta nästa datum för en viss veckodag
function getNextDateForDay($dayName, $referenceDate, $maxDays = 365) {
    $dates = [];
    $currentDate = clone $referenceDate;

    for ($i = 0; $i < $maxDays; $i++) {
        if (strcasecmp($currentDate->format('l'), $dayName) === 0) {
            $dates[] = [
                'date' => $currentDate->format('Y-m-d'),
                'day' => $currentDate->format('l')
            ];
        }
        $currentDate->modify('+1 day');
    }
    return $dates;
}

$groupedSlots = [];
$futureDays = 60; // Antal dagar framåt
foreach ($timeSlots as $slot) {
    $day = $slot['day'];
    $fromTime = $slot['from_time'];

    $futureDates = getNextDateForDay($day, $today, $futureDays);
    foreach ($futureDates as $nextDateInfo) {
        $date = $nextDateInfo['date'];

        $slotDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $date . ' ' . $fromTime);

        if ($date == $today->format('Y-m-d') && $slotDateTime <= $today) {
            continue;
        }

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
}


// Sortera datumen
uksort($groupedSlots, function ($a, $b) {
    return strtotime($a) - strtotime($b);
});

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedDate = $_POST['selectedDate'] ?? null;
    $selectedTime = $_POST['selectedTimeSlot'] ?? null;

    
    $ombokadData=[
        'practitioner' => $practitioner,
        'time' => $timeData,
        'date' => $DateData,
        'appointment_date' => $selectedDate,
        'appointment_time' => $selectedTime
    ];
    $_SESSION['ombokadTid'] = $ombokadData;

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
    <link rel="stylesheet" href="../Stylesheets/bokaStyle.css">
    <link rel="stylesheet" href="../Stylesheets/headerStyle.css">
    <link rel="stylesheet" href="../Stylesheets/footerStyle.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <title>Omboka tid</title>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const groupedSlots = <?= json_encode($groupedSlots) ?>;

            // Initialisera kalender
            const calendar = flatpickr("#datePicker", {
                enable: Object.keys(groupedSlots),
                dateFormat: "Y-m-d",
                onChange: function (selectedDates, dateStr, instance) {
                    // Hantera valt datum
                    const allSlots = document.querySelectorAll('.time-slots');
                    allSlots.forEach(slot => {
                        if (slot.dataset.date === dateStr) {
                            slot.style.display = 'block';
                        } else {
                            slot.style.display = 'none';
                        }
                    });

                    // Uppdatera dolda inputfältet
                    const dateInput = document.getElementById('selectedDateInput');
                    if (dateInput) {
                        dateInput.value = dateStr;
                    }
                }
            });
        });
    </script>
</head>
<body>
    <?php echoHead() ?>
    <h1>Omboka din tid</h1>

    <form method="POST">
        <div id="bookingMaster">
            <div id="centerForm">
                <div id="daySelect">
                    <label for="datePicker">Välj ett datum:</label>
                    <input type="text" id="datePicker" placeholder="Välj datum">
                </div>

                <?php foreach ($groupedSlots as $date => $info): ?>
                <div class="time-slots" data-date="<?= htmlspecialchars($date) ?>" style="display: none;">
                    <h2>Tillgängliga tider för <?= htmlspecialchars($info['day']) ?> (<?= htmlspecialchars($date) ?>)</h3>
                    <div class="radiogrid">
                            <?php foreach ($info['slots'] as $slot): ?>
                                <?php $fromTime = htmlspecialchars($slot['from_time']); ?>
                                <div class="radio-grid-item">
                                    <label class="radiobutton">
                                        <input type="radio" class="radioinput" name="selectedTimeSlot" value="<?= $fromTime ?>" required>
                                        <?= $fromTime ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                </div>
                <?php endforeach; ?>

                <input type="hidden" id="selectedDateInput" name="selectedDate" value="">
                <button type="submit" id="timeSub">Uppdatera bokning</button>
            </div>
        </div>
    </form>
    <?php echoFooter() ?>
</body>
</html>