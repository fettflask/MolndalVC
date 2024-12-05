<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

session_start();

$cookiepath = "/tmp/cookies.txt";
$baseurl = 'http://193.93.250.83:8080/';

// Logga in till API:t
try {
    $ch = curl_init($baseurl . 'api/method/login');
} catch (Exception $e) {
    header("Location: http://193.93.250.83/wwwit-utv/Grupp%206/Sidor/errorPage.php");
    exit;
}

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"usr":"a23jaced@student.his.se", "pwd":"lmaokraftwerkvem?"}');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);

$response = curl_exec($ch);
if (curl_errno($ch)) {
    header("Location: http://193.93.250.83/wwwit-utv/Grupp%206/Sidor/errorPage.php");
    exit;
}
curl_close($ch);

// Funktion för att hämta alla practitioners och deras detaljer
function getPractitionerDetails($baseurl, $cookiepath) {
    $url = $baseurl . 'api/resource/Healthcare%20Practitioner?limit_page_length=None';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
    ]);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return null;
    }

    curl_close($ch);

    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE || empty($data['data'])) {
        return null;
    }

    $practitioners = $data['data'];
    $details = [];

    foreach ($practitioners as $practitioner) {
        $practitionerUrl = $baseurl . 'api/resource/Healthcare%20Practitioner/' . rawurlencode($practitioner['name']);
        $ch = curl_init($practitionerUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
        ]);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);

        $practitionerResponse = curl_exec($ch);
        if (curl_errno($ch)) {
            return null;
        }

        curl_close($ch);

        $practitionerData = json_decode($practitionerResponse, true);
        if (!empty($practitionerData['data'])) {
            $details[] = $practitionerData['data'];
        }
    }

    return $details;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $practitioners = getPractitionerDetails($baseurl, $cookiepath);
    if ($practitioners === null) {
        header("Location: http://193.93.250.83/wwwit-utv/Grupp%206/Sidor/errorPage.php");
        exit;
    }

    $selectedPractitionerName = $_POST['selectedPractitioner'] ?? '';
    $practitionerKod = '';

    foreach ($practitioners as $practitioner) {
        if ($practitioner['practitioner_name'] === $selectedPractitionerName) {
            $practitionerKod = $practitioner['practitioner_schedules'][0]['parent'] ?? '';
            break;
        }
    }

    if (empty($practitionerKod)) {
        header("Location: http://193.93.250.83/wwwit-utv/Grupp%206/Sidor/errorPage.php");
        exit;
    }

    $status = 'Open';
    $appointmentType = 'Vård (G6)';

    $selectedDate = $_POST['selectedDate'] ?? '';
    $selectedTimeSlot = $_POST['selectedTimeSlot'] ?? '';
    $company = $_POST['company'] ?? '';
    $department = $_POST['department'] ?? '';
    $appointmentFor = $_POST['appointment_For'] ?? '';
    $patientName = $_POST['patient'] ?? '';

    $title = $patientName . ' with ' . $selectedPractitionerName;
    $appointmentDatetime = $selectedDate . ' ' . $selectedTimeSlot;
    $serviceUnit = 'Almänmottagnings Rum 1 - MV';

    $bookingData = [
        'title' => $title,
        'status' => $status,
        'appointment_type' => $appointmentType,
        'appointment_for' => $appointmentFor,
        'company' => $company,
        'practitioner' => $practitionerKod,
        'practitioner_name' => $selectedPractitionerName,
        'department' => $department,
        'appointment_date' => $selectedDate,
        'appointment_time' => $selectedTimeSlot,
        'appointment_datetime' => $appointmentDatetime,
        'patient' => $patientName,
        'service_unit' => $serviceUnit,
    ];

    $apiUrl = $baseurl . 'api/resource/Patient%20Appointment';
    $payload = json_encode($bookingData);

    try {
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
        ]);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code !== 200) {
            header("Location: http://193.93.250.83/wwwit-utv/Grupp%206/Sidor/errorPage.php");
            exit;
        }
    } catch (Exception $e) {
        header("Location: http://193.93.250.83/wwwit-utv/Grupp%206/Sidor/errorPage.php");
        exit;
    }
}

header("Location: http://193.93.250.83/wwwit-utv/Grupp%206/Sidor/bokningsHantering.php");
exit;
?>
