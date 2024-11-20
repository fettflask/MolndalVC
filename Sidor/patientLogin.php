<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../IMG/favicon.png">
    <link rel="stylesheet" href="../Stylesheets/headerStyle.css">
    <title>Mölndals Vårdcentral</title>
    <link rel="stylesheet" href="../Stylesheets/style.css">

</head>
<body>
    <header>
        <div id="companylogo">
            <a href="index.php">
                <img src="../IMG/MölndalLogo.png">
            </a>
        </div>

        <div id="topnav">
            <div class="navbox">
                <a href="">Nyheter</a>
            </div>

            <div class="navbox">
                <a href="">Sjukdomar & Besvär</a>
            </div>

            <div class="navbox">
                <a href="">Hälsoråd & Tips</a>
            </div>

            <div class="navbox">
                <a href="">Mer</a>
            </div>
        </div>

        <div class="navbutton" id="push">
            <a href="minaSidor.php">MINA SIDOR</a>
        </div>

        <div class="navbutton" id="buffer">
            <a href="">SÖK VÅRD</a>
        </div>
    </header>
    <main>
        <div id="loginForm">
            <h3>Patient Inlogg</h3>
            <form method='post' action='patientLoggedIn.php'>
                <table id="inloggTable">
                    <?php
                    if(isset($_SESSION["error"])){
                        echo "<tr>" .  $_SESSION["error"] . "</tr>";
                        session_unset();
                    }
                    ?>
                    <tr>
                        <div>
                            <td>
                                Personnummer
                            </td>
                            <td>
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

                            </td>
                        </div>
                    </tr>
                </table>
                <div id="inlogg">
                    <input type='submit' value='Öppna BankID'>
                </div>
            </form>
            <div>
                <span>Har du inget konto? Klicka <a href="skapaAnvändare.php">här!</a></span>
            </div>
        </div>
    </main>
</body>
</html>