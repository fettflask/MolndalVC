<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

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
        echo 'Curl error: ' . curl_error($ch);
        exit;
    }

    curl_close($ch);

    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "JSON Decode Error: " . json_last_error_msg();
        exit;
    }

    if (!empty($data['data'])) {
        $practitioners = $data['data'];
        $details = [];

        // Gå in i varje practitioner's detaljer
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
                echo 'Curl error: ' . curl_error($ch);
                exit;
            }

            curl_close($ch);

            $practitionerData = json_decode($practitionerResponse, true);
            if (!empty($practitionerData['data'])) {
                $details[] = $practitionerData['data'];
            }
        }

        return $details;
    } else {
        echo "Inga practitioners hittades.";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h2>Debug: POST-data</h2>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    // Hämta alla practitioners och deras detaljer
    $practitioners = getPractitionerDetails($baseurl, $cookiepath);
    echo "<h2>Debug: Practitioner Details</h2>";
    echo "<pre>";
    print_r($practitioners);
    echo "</pre>";

    // Använd vald practitioner's "parent" från practitioner_schedules
    $selectedPractitionerName = $_POST['selectedPractitioner'] ?? '';
    $practitionerKod = '';

    foreach ($practitioners as $practitioner) {
        if ($practitioner['practitioner_name'] === $selectedPractitionerName) {
            $practitionerKod = $practitioner['practitioner_schedules'][0]['parent'] ?? '';
            break;
        }
    }

    if (empty($practitionerKod)) {
        echo "Ingen matchande practitioner hittades för det valda namnet.";
        exit;
    }

    // Hårdkodade värden
    $status = 'Open';
    $appointmentType = 'Vård (G6)';

    // Värden från formuläret
    $selectedDate = $_POST['selectedDate'] ?? '';
    $selectedTimeSlot = $_POST['selectedTimeSlot'] ?? '';
    $company = $_POST['company'] ?? '';
    $department = $_POST['department'] ?? '';
    $appointmentFor = $_POST['appointment_For'] ?? '';
    $patientName = $_POST['patient'] ?? '';



    $title = $patientName . ' with ' . $selectedPractitionerName;
    $appointmentDatetime = $selectedDate . ' ' . $selectedTimeSlot;
    $serviceUnit = 'Almänmottagnings Rum 1 - MV';
    $patientNameFull = $patientName . ' (G6)';

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

    echo "<h2>Debug: Booking Data</h2>";
    echo "<pre>";
    print_r($bookingData);
    echo "</pre>";

    $apiUrl = $baseurl . 'api/resource/Patient%20Appointment';
    $payload = json_encode($bookingData);

    echo "<h2>Debug: JSON Payload</h2>";
    echo "<pre>" . json_encode($bookingData, JSON_PRETTY_PRINT) . "</pre>";

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

        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
            exit;
        }

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        echo "<h2>Debug: API Response</h2>";
        echo "HTTP Status Code: $http_code<br>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";

        $responseData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "JSON Decode Error: " . json_last_error_msg();
            exit;
        }

        if ($http_code === 200 && !empty($responseData['success'])) {
            header("Location: http://193.93.250.83/wwwit-utv/Grupp%206/Sidor/bokningsHantering.php");
        } else {
            $errorMessage = $responseData['message'] ?? 'Okänt fel';
            echo "Ett fel uppstod vid bokning: $errorMessage";
        }
    } catch (Exception $e) {
        echo 'Caught exception: ', $e->getMessage(), "\n";
    }
} else {
    echo "Fel metod.";
    exit;
}
?>
