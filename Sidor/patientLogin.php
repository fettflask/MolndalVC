<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../Stylesheets/style.css">

</head>
<body>
    <header>
        <h1>Logga in</h1>
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