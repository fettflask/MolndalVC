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
                    <tr id="användarnamn">
                        <div>
                            <td>
                                Personnummer
                            </td>
                            <td>
                                <input type='text' name='pnr'>
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