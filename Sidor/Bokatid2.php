<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'Funktioner/funktioner.php';

// Logga in till API:t
curlSetup();

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

// Se om formuläret fungerade
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedPatient = $_POST['selectedPatient'] ?? '';
    $selectedPractitioner = $_POST['selectedPractitioner'] ?? '';

    if (!$selectedPractitioner) {
        echo "Ingen läkare vald.";
        exit;
    }               
    
    // Hämta schema för vald läkare
    $scheduleData = curlGetData('api/resource/Practitioner%20Schedule?filters={"schedule_name":"' . rawurlencode($selectedPractitioner) .'"}&fields=["time_slots.from_time","time_slots.from_time","time_slots.day"]&limit_page_length=None&order_by=from_time');
    
    $timeSlots = $scheduleData['data'] ?? [];
    if (empty($timeSlots)) {
        echo 'Inga tidsluckor tillgängliga för vald läkare.';
        exit;
    }

    // Hämta bokningar
    curlSetup();

    // Hämta bokningar för vald läkare
    $bookedSlots = [];
     
    $appointmentDetails = curlGetData('api/resource/Patient%20Appointment?filters={"practitioner_name":"' . rawurlencode($selectedPractitioner) .'"}&fields=["practitioner_name","practitioner","appointment_date","appointment_time"]&limit_page_length=None');
    $appointmentDetails = $appointmentDetails['data'];
    foreach($appointmentDetails as $row2){
        if ($row2['practitioner_name'] === $selectedPractitioner || $row2['practitioner'] === $selectedPractitioner) {
            $date = $row2['appointment_date'];
            $time = $row2['appointment_time'];
            if (!isset($bookedSlots[$date])) {
                $bookedSlots[$date] = [];
            }
            $bookedSlots[$date][] = $time;
        }
    }

    // Filtrera schematider mot bokade tider
    $today = new DateTime(); // Nuvarande datum och tid
    date_default_timezone_set('Europe/Stockholm');

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
    <link rel="stylesheet" href="../Stylesheets/headerStyle.css">
    <link rel="stylesheet" href="../Stylesheets/bokaStyle.css">
    <link rel="stylesheet" href="../Stylesheets/footerStyle.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <title>Schema för <?= htmlspecialchars($selectedPractitioner) ?></title>
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
    <h1>Tillgängliga tider för <?= htmlspecialchars($selectedPractitioner) ?></h1>

    <div id="bookingMaster">
        <div id="centerForm">
            <div id="daySelect">
                <label for="datePicker">Välj ett datum:</label>
                <input type="text" id="datePicker" placeholder="Välj datum">
            </div>

            <form action="OkäntFel300.php" method="POST">
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
                
                <input type="submit" id="timeSub" value='Boka tid'>
            </form>
        </div>
    </div>

    <?php echoFooter() ?>
</body>
</html>
