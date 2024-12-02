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
    <link rel="stylesheet" href="../Stylesheets/indexStyle.css">
    <link rel="stylesheet" href="../Stylesheets/headerStyle.css">
    <link rel="stylesheet" href="../Stylesheets/footerStyle.css">
    <title>Mölndals Vårdcentral</title>
</head> 
<body>
    <?php echoHead(); ?>

    <main>
    <section id="split">
        <div id="left">
            <div id="leftP">
                <h1>Din vårdcentral <br> i Mölndal</h1>
                Mauris tempor sit amet neque quis fringilla. Curabitur luctus scelerisque velit, 
                sed blandit nisi dapibus sed.
            </div>
        </div>
        <div id="right"></div>
    </section>

    <section class="Tjänster">
        <h3>Våra tjänster</h3>

        <ul>
            <li>Vaccination mot Influensa och Covid-19</li>
            <li>Provtaging</li>
            <li>TBE-Vaccination</li>
            <li>Allergi och överkänslighet</li>
            <li>Astma/KOL</li>
            <li>Demens</li>
            <li>Injektioner</li>
            <li>Intyg</li>
            <li>Drop-in</li>
            <li>Psykisk hälsa och psykoterapi</li>
            <li>Rehabkoordinator</li>
            <li>Sår och stygn</li>
        </ul>
    </section>

    <section class="KontaktInfo">
        <h3>Kontakta oss</h3>
        <p>
            Har du frågor eller vill boka en tid? Tveka inte att höra av dig!
        </p>
        <p>
            <strong>Telefon:</strong> ??? ??? ????<br>
            <strong>E-post:</strong> ?
        </p>
        <p>
            <strong>Adress:</strong> The white house
        </p>
    </section>

    <section class="Öppettider">
    <h3>Adress & öppettider</h3>
    <p><strong>Adress:</strong> Bergmansgatan 17-23, 431 30, Mölndal</p>
    <p><strong>Öppettider:</strong></p>
    <ul>
        <li>Måndag kl. 7.45-19.00</li>
        <li>Tisdag kl. 7.45-19.00</li>
        <li>Onsdag kl. 7.45-19.00</li>
        <li>Torsdag kl. 7.45-19.00</li>
        <li>Fredag kl. 7.45-17.45</li>
    </ul>
    <p><strong>Öppettider till Drop-in:</strong> Helgfria vardagar 8.00-15.00.</p>
    <p><strong>Avvikande öppettider:</strong> Röda dagar är vårdcentralen stängd. Vid akuta besvär, kontakta din jourmottagning eller akutmottagning.</p>
    </section>
</main>
    
<?php echoFooter(); ?>

</body>
