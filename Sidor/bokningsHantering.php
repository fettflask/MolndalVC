<?php
session_start();
error_reporting(E_ALL);

include 'Funktioner/funktioner.php';
if(!isset($_SESSION["namn"])){
    header("Location: patientLogin.php");
    die();
}


// Sätt upp API-bas och cookies
$cookiepath = "/tmp/cookies.txt";
$baseurl = 'http://193.93.250.83:8080/';

// Logga in till API:t
curlSetup();

// Hämta användarens bokningar
$anvandarnamn = str_replace(" ", "%20", $_SESSION["namn"]);

$allAppointments = getAllAppointments($anvandarnamn);

//Bubblesort sorterar efter de la time
for($i = 0; $i < sizeof($allAppointments); $i++){
    for($j = 0; $j < sizeof($allAppointments)-1; $j++){
        $tempArray = [];
        if($allAppointments[$i]["appointment_date"] < $allAppointments[$j]["appointment_date"]){
            $tempArray = $allAppointments[$j];
            $allAppointments[$j] = $allAppointments[$i];
            $allAppointments[$i] = $tempArray;
        } 
        else if($allAppointments[$i]["appointment_date"] == $allAppointments[$j]["appointment_date"] && $allAppointments[$i]["appointment_time"] > $allAppointments[$j]["appointment_time"]){
            $tempArray = $allAppointments[$j];
            $allAppointments[$j] = $allAppointments[$i];
            $allAppointments[$i] = $tempArray;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../IMG/favicon.png">
    <link rel="stylesheet" href="../Stylesheets/headerStyle.css">
    <link rel="stylesheet" href="../Stylesheets/bokningarStyle.css">
    <link rel="stylesheet" href="../Stylesheets/footerStyle.css">
    <title>Mina Bokningar</title>
</head>
<style>
    .message-box-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    }

    .message-box {
        border: 2px solid  rgb(13, 48, 80);
        padding: 20px;
        border-radius: 8px;
        background-color: #f9f9f9;
        max-width: 600px;
        margin: 0 auto;
        text-align: center;
    }
    .message-box h1 {
        color:  rgb(13, 48, 80);
    }
    .message-box p {
        font-size: 18px;
    }
</style>
<script>
    function hideMessageBox() {
        const messageBox = document.getElementById('messageBox');
        if (messageBox) {
            messageBox.style.display = 'none';
        }
        
    }
</script>

<body>
    <?php echoHead(); ?>

    <h1>Dina Bokningar</h1>

    <?php
        if (isset($_SESSION['bokadTid'])) {
            echo '<div class="message-box-overlay" id="messageBox">';
                echo '<div class="message-box">';
                    echo '<h3>Tid bokad med ' . $_SESSION['bokadTid']['practitioner_name'] . '</h3>';
                    echo '<p>Datum: ' . $_SESSION['bokadTid']['appointment_date'] . '</p>';
                    echo '<p>Tid: ' . $_SESSION['bokadTid']['appointment_time'] . '</p>';
                    echo '<button class="redirect-button" onclick="hideMessageBox()">Bekräfta</button>';
                echo '</div>';
            echo '</div>';
            unset($_SESSION['bokadTid']);
        }
    ?>

    <?php if (!empty($allAppointments)):?>
    <div id="bokningarMaster">
        <?php foreach ($allAppointments as $booking): ?>
            <div id="bokningarElement">
                <div id="status">
                    <strong>Bokning:</strong> <?php echo htmlspecialchars($booking['title']); ?><br>
                    <strong>Datum:</strong> <?php echo htmlspecialchars($booking['appointment_date']); ?><br>
                    <strong>Tid:</strong> <?php echo htmlspecialchars($booking['appointment_time']); ?><br>
                    <strong>Status:</strong> <?php echo htmlspecialchars($booking['status']); ?><br>
                </div>
                <div id="buttonMaster"> 
                    <a class="buttonSlave" 
                        href="editBokning6.php?booking_id=<?php echo urlencode($booking['name']); ?>
                        &practitioner_name=<?php echo urlencode($booking['practitioner_name']); ?>">
                        Omboka
                    </a>
                <form method="POST" action="deleteBooking.php" style="display:inline;">
                    <input type="hidden" name="appointmentId" value="<?php echo htmlspecialchars($booking['name']); ?>">
                <button type="submit" class="buttonSlave" onclick="return confirm('Är du säker på att du vill ta bort denna bokning? \nVARNING! Om bokningen är inom 24 timmar kommer du att debiteras.');">Avboka</button>                
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

