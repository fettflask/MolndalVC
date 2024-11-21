<?php
    session_start();
    session_destroy();

    if(!isset($_SESSION["namn"])){
        header("Location: patientLogin.php");
        $_SESSION["error"] = "Felaktikt personnummer";
        die();
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../IMG/favicon.png">
    <link rel="stylesheet" href="../Stylesheets/headerStyle.css">
    <link rel="stylesheet" href="../Stylesheets/minaSidorStyle.css">
    <title>Mina Sidor</title>
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

        <div id="namn">
            <?php
                echo "Välkommen, " . $_SESSION["namn"];
            ?>
        </div>
    </header>

    <main>
        <h2>Hur kan vi hjälpa dig?</h2>
        <div id="mastergrid">
            <div class="gridelement">
                hej
            </div>
            <div class="gridelement">
                HALLÅ
            </div>
            <div class="gridelement">
                Tjena
            </div>
            <div class="gridelement">
                Morsning
            </div>
        </div>
    </main>

</body>
</html>