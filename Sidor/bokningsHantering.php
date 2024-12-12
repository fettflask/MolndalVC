<?php
session_start();
error_reporting(E_ALL);

include 'Funktioner/funktioner.php';
if(!isset($_SESSION["namn"])){
    header("Location: patientLogin.php");
    die();
}

//Tar bort bokning om användare valt att göra det
if(isset($_POST["appointmentId"])){
    deleteAppointment($_POST["appointmentId"]);
}

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
        font-family: 'Roboto', sans-serif;
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
    .redirect-button {
        margin-top: 15px;
        margin-left :5px;
        margin-right :5px;
        padding: 10px 20px;
        background-color: rgb(13, 48, 80);
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .tdTop{
        border: 2px solid rgb(13, 48, 80);
        border-radius:10px;
        padding-left:10px;
        padding-right:10px;
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
<script>
    let formToSubmit = null;
    let linkToSend = null;

    function showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
        }
    }

    function hideModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
        }
    }

    function confirmOmboka(event) {
        event.preventDefault();
        linkToSend = event.target.closest('a');
        showModal('confirmOmbokaModal');
    }

    function confirmAvboka(event) {
        event.preventDefault();
        formToSubmit = event.target;
        showModal('confirmAvbokaModal');
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('cancelAvbokaButton').addEventListener('click', () => {
            hideModal('confirmAvbokaModal');
            formToSubmit = null;
        });

        document.getElementById('confirmAvbokaButton').addEventListener('click', () => {
            hideModal('confirmAvbokaModal');
            if (formToSubmit) {
                formToSubmit.submit();
                formToSubmit = null;
            }
        });

        document.getElementById('cancelOmbokaButton').addEventListener('click', () => {
            hideModal('confirmOmbokaModal');
            linkToSend = null;
        });

        document.getElementById('confirmOmbokaButton').addEventListener('click', () => {
            hideModal('confirmOmbokaModal');
            if (linkToSend) {
                window.location.href = linkToSend.href;
                linkToSend = null;
            }
        });
    });
</script>


<body>
    <?php echoHead(); ?>

    <h1>Dina Bokningar</h1>

    <?php
        if (isset($_SESSION['bokadTid'])) {
            echo '<div class="message-box-overlay"  id="messageBox">';
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

    <?php
        if (isset($_SESSION['raderadTid'])) {
            echo '<div class="message-box-overlay"  id="messageBox">';
                echo '<div class="message-box">';
                    echo '<h3>Din tid med ' . $_SESSION['raderadTid']['practitioner_name'] . ' är nu avbokad</h3>';
                    echo '<p>Datum: ' . $_SESSION['raderadTid']['appointment_date'] . '</p>';
                    echo '<p>Tid: ' . $_SESSION['raderadTid']['appointment_time'] . '</p>';
                    echo '<button class="redirect-button" onclick="hideMessageBox()">Bekräfta</button>';
                echo '</div>';
            echo '</div>';
            unset($_SESSION['raderadTid']);
        }
    ?>

    <?php
        if (isset($_SESSION['ombokadTid'])) {
            echo '<div class="message-box-overlay" id="messageBox">';
                echo '<div class="message-box">';
                    echo '<h3>Din tid med ' . $_SESSION['ombokadTid']['practitioner'] . ' är nu ombokad</h3>';
                    echo '<table style="margin:auto;"><tr><td><p>Från</p></td><td><p>→</p></td><td><p>Till</p></td></tr><tr>';
                    echo '<td class="tdTop"><p>' . $_SESSION['ombokadTid']['date'] . '</p><p>' . $_SESSION['ombokadTid']['time'] . '</p></td>';
                    echo '<td></td>';
                    echo '<td class="tdTop"><p>' . $_SESSION['ombokadTid']['appointment_date'] . '</p><p>' . $_SESSION['ombokadTid']['appointment_time'] . '</p></td>';
                    echo '</tr></table>';
                    echo '<button class="redirect-button" onclick="hideMessageBox()">Bekräfta</button>';
                echo '</div>';
            echo '</div>';
            unset($_SESSION['ombokadTid']);
        }
    ?>


    <div class="message-box-overlay" id="confirmAvbokaModal" style="display: none;">
        <div class="message-box">
            <h3>Är du säker på att du vill avboka denna tid?</h3>
            <p>Varning: sker avbokningen inom 24 timmar innan den bokade tiden kommer du debiteras.</p>
            <button class="redirect-button" id="cancelAvbokaButton">Behåll bokning</button>
            <button class="redirect-button" id="confirmAvbokaButton">Avboka</button>
        </div>
    </div>

    <div class="message-box-overlay" id="confirmOmbokaModal" style="display: none;">
        <div class="message-box">
            <h3>Är du säker på att du vill omboka denna tid?</h3>
            <p>Varning: sker ombokningen inom 24 timmar innan den bokade tiden kommer du debiteras.</p>
            <button class="redirect-button" id="cancelOmbokaButton">Behåll bokning</button>
            <button class="redirect-button" id="confirmOmbokaButton">Omboka</button>
        </div>
    </div>

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
                        &practitioner_name=<?php echo urlencode($booking['practitioner_name']); ?>
                        &appointment_time=<?php echo urlencode($booking['appointment_time']); ?>
                        &appointment_date=<?php echo urlencode($booking['appointment_date']); ?>"
                        onclick="confirmOmboka(event);">
                        Omboka
                    </a>

                    <form method="POST" action="bokningsHantering.php" style="display:inline;" onsubmit="confirmAvboka(event);">
                        <input type="hidden" name="appointmentId" value="<?php echo htmlspecialchars($booking['name']); ?>">
                        <button type="submit" class="buttonSlave" style="cursor: pointer;">Avboka</button>
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

