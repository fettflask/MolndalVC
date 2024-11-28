<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Symptomformulär</title>
</head>
<body>
    <h1>Övergripande symptom för tidbokning online</h1>

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
            header("Location: tiderKanske.php");
            exit();
        } else {
            header("Location: Telefontid.php");
            exit();
        }
    }
    ?>

    <form method="post">
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
            <textarea id="description" name="description" rows="4" cols="50" maxlength="150" placeholder="Skriv dina besvär här..."></textarea>
        </p>
        
        <p>
            <button type="submit">Skicka in</button>
        </p>
    </form>
</body>
</html>
