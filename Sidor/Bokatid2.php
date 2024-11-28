<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$cookiepath = "/tmp/cookies.txt";
$baseurl = 'http://193.93.250.83:8080/';

// Inloggningsfunktion
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

// Logga in till API:t
apiLogin($baseurl, $cookiepath);

// Se om formuläret fungerade
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedPatient = $_POST['selectedPatient'] ?? '';
    $selectedPractitioner = $_POST['selectedPractitioner'] ?? '';

    if (!$selectedPractitioner) {
        echo "Ingen läkare vald.";
        exit;
    }

    // Hämta schema för vald läkare
    $scheduleUrl = $baseurl . 'api/resource/Practitioner%20Schedule/' . rawurlencode($selectedPractitioner);
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

    // Hämta bokningar
    apiLogin($baseurl, $cookiepath);

    $appointmentsUrl = $baseurl . 'api/resource/Patient%20Appointment/?limit_page_length=None';
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

    // Hämta bokningar för vald läkare
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
        if ($appointmentDetails['practitioner_name'] === $selectedPractitioner || $appointmentDetails['practitioner'] === $selectedPractitioner) {
            $date = $appointmentDetails['appointment_date'];
            $time = $appointmentDetails['appointment_time'];
            if (!isset($bookedSlots[$date])) {
                $bookedSlots[$date] = [];
            }
            $bookedSlots[$date][] = $time;
        }
    }

    // Filtrera schematider mot bokade tider
    $today = new DateTime();

    function getNextDateForDay($dayName, $referenceDate) {
        $dayOfWeek = $referenceDate->format('l'); 
        $daysToAdd = (date('N', strtotime($dayName)) - date('N', strtotime($dayOfWeek)) + 7) % 7;
        $nextDate = clone $referenceDate;
        $nextDate->modify("+$daysToAdd days");

        return [
            'date' => $nextDate->format('Y-m-d'), 
            'day' => $nextDate->format('l') 
        ];
    }

    $groupedSlots = [];
    foreach ($timeSlots as $slot) {
        $day = $slot['day'];
        $fromTime = $slot['from_time'];
        $nextDateInfo = getNextDateForDay($day, $today);
        $date = $nextDateInfo['date'];

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
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schema för <?= htmlspecialchars($selectedPractitioner) ?></title>
    <script>
        function updateTimeSlots() {
            // Hämta valt datum från dropdown
            const selectedDate = document.getElementById('dateDropdown').value;

            // Visa eller dölj tidsluckor baserat på valt datum
            const allSlots = document.querySelectorAll('.time-slots');
            allSlots.forEach(slot => {
                if (slot.dataset.date === selectedDate) {
                    slot.style.display = 'block'; // Visa matchande slots
                } else {
                    slot.style.display = 'none'; // Dölj andra slots
                }
            });

    // Hämta dolda inputfältet och uppdatera dess värde
    const dateInput = document.getElementById('selectedDateInput');
    if (dateInput) {
        dateInput.value = selectedDate; // Sätt valt datum i inputfältet
    }
}

    </script>
</head>
<body>
    <h1>Schema för <?= htmlspecialchars($selectedPractitioner) ?></h1>
    <p>Vald patient: <?= htmlspecialchars($selectedPatient) ?></p>

    <!-- Dropdownen för datum -->
    <label for="dateDropdown">Välj ett datum:</label>
    <select id="dateDropdown" onchange="updateTimeSlots()">
        <option value="">-- Välj datum --</option>
        <?php foreach ($groupedSlots as $date => $info): ?>
            <option value="<?= htmlspecialchars($date) ?>">
                <?= htmlspecialchars($info['day']) ?> (<?= htmlspecialchars($date) ?>)
            </option>
        <?php endforeach; ?>
    </select>

    <form action="aye2.php" method="POST">
    <?php foreach ($groupedSlots as $date => $info): ?>
        <div class="time-slots" data-date="<?= htmlspecialchars($date) ?>" style="display: none;">
            <h2>Tider för <?= htmlspecialchars($info['day']) ?> (<?= htmlspecialchars($date) ?>):</h2>
            <?php foreach ($info['slots'] as $slot): ?>
                <?php $fromTime = htmlspecialchars($slot['from_time']); ?>
                <label>
                    <input type="radio" name="selectedTimeSlot" value="<?= $fromTime ?>" required>
                    <?= $fromTime ?>
                </label>
                <br>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <input type="hidden" id="selectedDateInput" name="selectedDate" value="">
    <input type="hidden" name="company" value="Mölndal VC(G6)">
    <input type="hidden" name="department" value="Allmänvård(G6)">
    <input type="hidden" name="appointment For" value="Practitioner">
    <input type="hidden" name="selectedPractitioner" value="<?= htmlspecialchars($selectedPractitioner) ?>">
    <input type="hidden" name="patient" value="<?= htmlspecialchars($selectedPatient) ?>">
    
    <button type="submit">Boka tidslucka</button>
</form>

</body>
</html>
