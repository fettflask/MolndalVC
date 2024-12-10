<?php
    session_start();
    include 'Funktioner/funktioner.php';
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../IMG/favicon.png">
    <link rel="stylesheet" href="../Stylesheets/headerStyle.css">
    <link rel="stylesheet" href="../Stylesheets/formStyle.css">
    <link rel="stylesheet" href="../Stylesheets/footerStyle.css">
    <title>Symptomformulär</title>
    <script>
        function tillMinaSidor() {
            window.location.href = "minaSidor.php";
        }
    </script>
</head>

<body>
    <?php echoHead(); ?>
    <div id="formMaster">
        <h1>Triage</h1>
        <p>
            För att själv boka en tid hos oss behöver du svara på några <br>
            frågor som bedömer om du ska få boka en läkartid.
            <div class="detailMaster">
                <details class="detailSlave">
                    <summary class="mainSummary">Varför måste jag svara på frågorna?</summary>
                    <p>
                        Läkartider är för de som behöver det. <br>
                        Utan en bedömning kan personer boka ett besök för ärenden som inte kräver ett fysikst läkarmöte.
                    </p>
                </details>
            </div>
            <div class="detailMaster">
                <details class="detailSlave">
                    <summary class="mainSummary">Vem gör triage bedömningen?</summary>
                    <p>
                        Applikationen använder den senaste AI-teknologin för att göra triagebedömningar, 
                        vilket innebär att den snabbt och noggrant prioriterar patienters vårdbehov baserat på symtom och medicinsk information.
                    </p>
                </details>
            </div>
        </p>

        <?php
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $fever = $_POST['fever'] ?? 'Nej';
                $cough = $_POST['cough'] ?? 'Nej';
                $bloodCough = $_POST['bloodCough'] ?? 'Nej';
                $breathing = $_POST['breathing'] ?? 'Nej';
                $pain = $_POST['pain'] ?? 'Nej';
                $sickDays = $_POST['sickDays'] ?? 'Nej';
                $description = $_POST['description'] ?? '';

                // omdirigering till boka tid eller telefontid, räcker med att det är ja på en av frågorna
                if ($sickDays === 'Ja' || $bloodCough === 'Ja' || $fever === 'Ja') {
                    header("Location: valAvLäkare.php");
                    exit();
                } else {
                    //header("Location: Telefontid.php");
                    echo '<div class="message-box-overlay">';
                        echo '<div class="message-box">';
                            echo '<h1>Du bedöms inte behöva läkartid. Vi hänvisar dig till vår telefonrådgivning.</h1>';
                            echo '<p>Ring detta nummer för att få hjälp:</p>';
                            echo '<p class="phone-number">0500-30 25 55</p>';
                            echo '<p>Tack för att du kontaktar oss!</p>';
                            echo '<button class="redirect-button" onclick="tillMinaSidor()">Mina Sidor</button>';
                        echo '</div>';
                    echo '</div>';
                    exit();
                }
            }
        ?>

        <form method="post" action="FormulärBokatidSTARTHÄR.php">
            <p>
                <label>Har du haft feber i över sju dygn?</label><br>
                <input type="radio" id="feverYes" name="fever" value="Ja">
                <label for="feverYes">Ja</label>
                <input type="radio" id="feverNo" name="fever" value="Nej">
                <label for="feverNo">Nej</label>
            </p>
            
            <p>
                <label>Har du hosta?</label><br>
                <input type="radio" id="coughYes" name="cough" value="Ja">
                <label for="coughYes">Ja</label>
                <input type="radio" id="coughNo" name="cough" value="Nej">
                <label for="coughNo">Nej</label>
            </p>
            
            <p>
                <label>Kommer det blod när du hostar?</label><br>
                <input type="radio" id="bloodCoughYes" name="bloodCough" value="Ja">
                <label for="bloodCoughYes">Ja</label>
                <input type="radio" id="bloodCoughNo" name="bloodCough" value="Nej">
                <label for="bloodCoughNo">Nej</label>
            </p>
            
            <p>
                <label>Känns det tungt när du andas?</label><br>
                <input type="radio" id="breathingYes" name="breathing" value="Ja">
                <label for="breathingYes">Ja</label>
                <input type="radio" id="breathingNo" name="breathing" value="Nej">
                <label for="breathingNo">Nej</label>
            </p>
            
            <p>
                <label>Har du muskelvärk och/eller huvudvärk?</label><br>
                <input type="radio" id="painYes" name="pain" value="Ja">
                <label for="painYes">Ja</label>
                <input type="radio" id="painNo" name="pain" value="Nej">
                <label for="painNo">Nej</label>
            </p>
            
            <p>
                <label>Har du varit sjuk i mer än 7 dagar?</label><br>
                <input type="radio" id="sickDaysYes" name="sickDays" value="Ja">
                <label for="sickDaysYes">Ja</label>
                <input type="radio" id="sickDaysNo" name="sickDays" value="Nej">
                <label for="sickDaysNo">Nej</label>
            </p>
            
            <p>
                <label>Beskriv dina besvär med max 150 ord:</label><br>
                <textarea id="description" name="description" rows="4" cols="50" maxlength="750" placeholder="Beskriv dina besvär här..."></textarea>
            </p>
            
            <input type="submit" id="sickSub" value='Skicka in'>
        </form>
    </div>


    <?php echoFooter(); ?>


</body>
</html>
