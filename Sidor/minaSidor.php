<?php
    session_start();
    include 'Funktioner/funktioner.php';

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
    <link rel="stylesheet" href="../Stylesheets/footerStyle.css">
    <title>Mina Sidor</title>
</head>
<body>
    <?php echoHead(); ?>

    <main>
        <?php
            echo '<h2>Hej, ' . $_SESSION["namn"] . '. Hur kan vi hjälpa dig?</h2>';
        ?>
        <div id="mastergrid">
            <div class="mastergridelement">
                <div class="gridelement">
                    <img class="svg" src="../IMG/calendar-add-svgrepo-com.svg">
                    <h3>Boka tid</h3>
                    <p>Du kan direkt boka ett läkarbesök hos oss om du bedöms behöva det.</p>
                    <!--Lägg adressen här-->
                    <a href="FormulärBokatidSTARTHÄR.php" class="pushdowndammit">
                        <div class="gridbutton">Boka tid</div>
                    </a>
                </div>
            </div>
            <div class="mastergridelement">
                <div class="gridelement">
                    <img class="svg" src="../IMG/jar-of-pills-svgrepo-com.svg">
                    <h3>Beställ läkemedel</h3>
                    <p>Du kan skicka en förfrågan om att förnya dina recept på läkemedel hos oss.</p>
                    <!--Lägg adressen här-->
                    <a href="recept.php" class="pushdowndammit">
                        <div class="gridbutton">Beställ</div>
                    </a>
                </div>
            </div>
            <div class="mastergridelement">
                <div class="gridelement">
                    <img class="svg" src="../IMG/book-bookmark-svgrepo-com.svg">
                    <h3>Min journal</h3>
                    <p>Här kan du ta andel av dina journalanteckningar.</p>
                    <!--Lägg adressen här-->
                    <a href="patientJournal.php" class="pushdowndammit">
                        <div class="gridbutton">Se journal</div>
                    </a>
                </div>
            </div>
            <div class="mastergridelement">
                <div class="gridelement">
                    <img class="svg" src="../IMG/syringe-svgrepo-com.svg">
                    <h3>Provresultat</h3>
                    <p>Här kan du ta andel av resultaten på dina prover.</p>
                    <!--Lägg adressen här-->
                    <a href="labResultat.php" class="pushdowndammit">
                        <div class="gridbutton">Se provresultat</div>
                    </a>
                </div>
            </div>
            <div class="mastergridelement">
                <div class="gridelement">
                    <img class="svg" src="../IMG/calendar-search-svgrepo-com.svg">
                    <h3>Mina tider</h3>
                    <p>Här kan du se alla dina bokade besök hos oss och av- eller omboka dem.</p>
                    <!--Lägg adressen här-->
                    <a href="bokningsHantering.php" class="pushdowndammit">
                        <div class="gridbutton">Se tider</div>
                    </a>
                </div>
            </div>
            <div class="mastergridelement">
                <div class="gridelement">
                    <img class="svg" src="../IMG/gallery-minimalistic-svgrepo-com.svg">
                    <h3>Lorem Ipsum</h3>
                    <p>Aliquam lorem purus, convallis quis turpis et, consectetur malesuada arcu.</p>
                    <!--Lägg adressen här-->
                    <a href="" class="pushdowndammit">
                        <div class="gridbutton">Botono</div>
                    </a>
                </div>
            </div>
            <div class="mastergridelement">
                <div class="gridelement">
                    <img class="svg" src="../IMG/gallery-minimalistic-svgrepo-com.svg">
                    <h3>Lorem Ipsum</h3>
                    <p>Aliquam lorem purus, convallis quis turpis et, consectetur malesuada arcu.</p>
                    <!--Lägg adressen här-->
                    <a href="" class="pushdowndammit">
                        <div class="gridbutton">Botono</div>
                    </a>
                </div>
            </div>
            <div class="mastergridelement">
                <div class="gridelement">
                    <img class="svg" src="../IMG/phone-svgrepo-com.svg">
                    <h3>Kontakt</h3>
                    <p>
                        Telefon:<br> 0701740620 <br><br>
                        Address:<br> Björkåsgatan 4 <br><br>
                        Mejl:<br> Kundservice@MölndalVC.se
                    </p>
                </div>
            </div>
        </div>
    </main>
    <?php echoFooter(); ?>

</body>
</html>