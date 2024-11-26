<?php
    session_start();
    include 'Funktioner/funktioner.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../IMG/favicon.png">
    <link rel="stylesheet" href="../Stylesheets/headerStyle.css">
    <link rel="stylesheet" href="../Stylesheets/loginStyle.css">
    <title>Mölndals Vårdcentral</title>

</head>
<body>
    <?php echoHead(); ?>

    <main>
        <div id="loginForm">
            <div id="elementCenter">
                <h1>Logga in</h1>
                <form method='post' action='patientLoggedIn.php'>
                    <?php
                    if(isset($_SESSION["error"])){
                        echo "<tr>" .  $_SESSION["error"] . "</tr>";
                        session_unset();
                    }
                    ?>
                        <div id="pnrfield">
                            Personnummer - ååååmmdd-nnnn
                                <input type='text' id="pnr" name='pnr' pattern="[0-9]{8}-[0-9]{4}" required maxlength="13">   
                            <script>
                                const pnrInput = document.getElementById("pnr");
                                pnrInput.addEventListener("input", function () {
                                    let value = pnrInput.value.replace(/\D/g, "");
                                    if (value.length > 8) {
                                        value = value.slice(0, 8) + "-" + value.slice(8, 12);
                                    }
                                    pnrInput.value = value;
                                });
                                </script>
                        </div>
                    <input type='submit' id="bankid" value='Öppna BankID'>
                </form>
                <div>
                    <span>Har du inget konto? <a href="skapaAnvändare.php">Registrera dig!</a></span>
                </div>
            </div>
        </div>
    </main>
</body>
</html>