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
    <h1>Välkommen till ännu en dag i paradiset!</h1>
</header>
<main>
    <div id="loginForm">
            <h3>Personal Inlogg</h3>
            <form method='post' action='personalLoggedIn.php'>
                <table id="inloggTable">
                <?php
                if(isset($_SESSION["error"])){
                    echo "<tr>" .  $_SESSION["error"] . "</tr>";
                    session_unset();
                }
                ?>
                    <tr id="användarnamn">
                        <div>
                            <td>
                                Användarnamn
                            </td>
                            <td>
                                <input type='text' name='namn'>
                            </td>
                        </div>
                    </tr>
                    <tr id="lösenord">
                        <div>
                            <td>
                                Lösenord
                            </td>
                            <td>
                                <input type='password' name='password'>
                            </td>
                        </div>
                    </tr>
                </table>
                <div id="inlogg">
                    <input type='submit' value='Logga in'>
                </div>
            </form>
        </div>
        

        
    </main>
</body>
</html>
