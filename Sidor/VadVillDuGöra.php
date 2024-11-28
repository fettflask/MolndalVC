<?php
    session_start();
    include '../../Funktioner/funktioner.php';
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Huvudsida</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        button {
            padding: 15px 30px;
            font-size: 16px;
            margin: 10px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background-color: #007BFF;
            color: white;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
    <link rel="stylesheet" href="../../Stylesheets/headerStyle.css">
    <link rel="icon" type="image/x-icon" href="../../IMG/favicon.png">
</head>
<body>
    <?php 
        echo '
        <header>

            <div id="companylogo">
                <a href="index.php">
                    <img src="../../IMG/MölndalLogo.png">
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
                    <a href="minaSidor.php">Mina sidor</a>
                </div>

                <div class="navbutton" id="buffer">
                    <a href="">Sök vård</a>
                </div>';

                
                if(isset($_SESSION["namn"])){
                    echo '<div class="navbutton">';
                            echo '<a href="sessionKill.php">Logga ut</a>';
                    echo '</div>';
                }
    
    echo '</header>';
    ?>
    <h1>Välkommen</h1>
    <p>Välj ett alternativ nedan:</p>
    <form action="FormulärBokatidSTARTHÄR.php" method="get">
        <button type="submit">Gör en ny bokning</button>
    </form>
    <form action="bokningsHantering.php" method="get">
        <button type="submit">Hantera dina bokningar</button>
    </form>
</body>
</html>
