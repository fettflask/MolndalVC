<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['appointmentId'])) {
        die('Inget boknings-ID mottaget.');
    }

    $appointmentId = $_POST['appointmentId'];

    $baseurl = 'http://193.93.250.83:8080/';
    $cookiepath = '/tmp/cookies.txt';

    function deleteAppointment($baseurl, $cookiepath, $appointmentId) {
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

        $data = json_decode($response, true);

        // ta bort
        $deleteUrl = $baseurl . "api/resource/Patient%20Appointment/$appointmentId";
        $ch = curl_init($deleteUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return 'Curl error: ' . curl_error($ch);
        }

        curl_close($ch);

        $deleteResponse = json_decode($response, true);

        if (isset($deleteResponse['message'])) {
            return null; 
        }

        return 'Ett fel uppstod vid borttagning.';
    }

    $result = deleteAppointment($baseurl, $cookiepath, $appointmentId);

    if ($result === null) {
        echo "<script>alert('Bokningen har tagits bort.'); window.location.href = 'bokningsHantering.php';</script>";
    } else {
        echo "<script>alert('Ett fel uppstod: $result'); window.location.href = 'bokningsHantering.php';</script>";
    }
}
?>
