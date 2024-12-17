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
    <div id="split">
        <div id="left">
            <div id="leftP">
                <h1>Din vårdcentral i Mölndal</h1>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
                Aenean condimentum tortor ante, sed laoreet elit rhoncus quis. 
                Duis sodales sit amet ipsum ac suscipit.
            </div>
        </div>
        <div id="right">
        </div>
    </div>

    <h1>Hur kan vi hjälpa dig?</h1>

    <section>
        <div id="mastergrid">
            <div class="mastergridelement">
                <div class="gridelement">
                    <img class="svg" src="../IMG/user-svgrepo-com.svg">
                        <h3>Mina Sidor</h3>
                        <p>Här kan du se allt som har med din vård att göra.</p>
                        <!--Lägg adressen här-->
                        <a href="minaSidor.php" class="pushdowndammit">
                            <div class="gridbutton">Mina sidor</div>
                        </a>
                </div>
            </div>
            <div class="mastergridelement">
                <div class="gridelement">
                <img class="svg" src="../IMG/calendar-add-svgrepo-com.svg">
                    <h3>Boka tid</h3>
                    <p>Du kan direkt boka ett läkarbesök hos oss om du bedöms behöva det.</p>
                    <!--Lägg adressen här-->
                    <a href="minaSidor.php" class="pushdowndammit">
                        <div class="gridbutton">Boka tid</div>
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
                        <div class="gridbuttonLesser">Botono</div>
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
                        <div class="gridbuttonLesser">Botono</div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <h1>Våra tjänster</h1>

    <div id="tjanster">
        <div class="tjanstMasterElement">
            <details class="tjanstElement">
                <summary class="mainSummary">Vaccination</summary>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean condimentum tortor ante, 
                sed laoreet elit rhoncus quis. Duis sodales sit amet ipsum ac suscipit. 
                Cras eu libero egestas, mattis leo tincidunt, aliquam dui. 
                Duis bibendum faucibus enim, in ullamcorper magna posuere sit amet.
            </details>
            <details class="tjanstElement">
                <summary class="mainSummary">Provtaging</summary>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean condimentum tortor ante, 
                sed laoreet elit rhoncus quis. Duis sodales sit amet ipsum ac suscipit. 
                Cras eu libero egestas, mattis leo tincidunt, aliquam dui. 
                Duis bibendum faucibus enim, in ullamcorper magna posuere sit amet.
            </details>
            <details class="tjanstElement">
                <summary class="mainSummary">KBT-Terapi</summary>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean condimentum tortor ante, 
                sed laoreet elit rhoncus quis. Duis sodales sit amet ipsum ac suscipit. 
                Cras eu libero egestas, mattis leo tincidunt, aliquam dui. 
                Duis bibendum faucibus enim, in ullamcorper magna posuere sit amet.
            </details>
            <details class="tjanstElement">
                <summary class="mainSummary">Allergi och överkänslighet</summary>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean condimentum tortor ante, 
                sed laoreet elit rhoncus quis. Duis sodales sit amet ipsum ac suscipit. 
                Cras eu libero egestas, mattis leo tincidunt, aliquam dui. 
                Duis bibendum faucibus enim, in ullamcorper magna posuere sit amet.
            </details>
            <details class="tjanstElement">
                <summary class="mainSummary">Astma/KOL</summary>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean condimentum tortor ante, 
                sed laoreet elit rhoncus quis. Duis sodales sit amet ipsum ac suscipit. 
                Cras eu libero egestas, mattis leo tincidunt, aliquam dui. 
                Duis bibendum faucibus enim, in ullamcorper magna posuere sit amet.
            </details>
        </div>
        <div class="tjanstMasterElement">
            <details class="tjanstElement">
                <summary class="mainSummary">Barnhälsovård</summary>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean condimentum tortor ante, 
                sed laoreet elit rhoncus quis. Duis sodales sit amet ipsum ac suscipit. 
                Cras eu libero egestas, mattis leo tincidunt, aliquam dui. 
                Duis bibendum faucibus enim, in ullamcorper magna posuere sit amet.
            </details>
            <details class="tjanstElement">
                <summary class="mainSummary">Diabetesvård</summary>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean condimentum tortor ante, 
                sed laoreet elit rhoncus quis. Duis sodales sit amet ipsum ac suscipit. 
                Cras eu libero egestas, mattis leo tincidunt, aliquam dui. 
                Duis bibendum faucibus enim, in ullamcorper magna posuere sit amet.
            </details>
            <details class="tjanstElement">
                <summary class="mainSummary">Fotvård</summary>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean condimentum tortor ante, 
                sed laoreet elit rhoncus quis. Duis sodales sit amet ipsum ac suscipit. 
                Cras eu libero egestas, mattis leo tincidunt, aliquam dui. 
                Duis bibendum faucibus enim, in ullamcorper magna posuere sit amet.
            </details>
            <details class="tjanstElement">
                <summary class="mainSummary">Sår och stygn</summary>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean condimentum tortor ante, 
                sed laoreet elit rhoncus quis. Duis sodales sit amet ipsum ac suscipit. 
                Cras eu libero egestas, mattis leo tincidunt, aliquam dui. 
                Duis bibendum faucibus enim, in ullamcorper magna posuere sit amet.
            </details>
            <details class="tjanstElement">
                <summary class="mainSummary">Intyg</summary>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean condimentum tortor ante, 
                sed laoreet elit rhoncus quis. Duis sodales sit amet ipsum ac suscipit. 
                Cras eu libero egestas, mattis leo tincidunt, aliquam dui. 
                Duis bibendum faucibus enim, in ullamcorper magna posuere sit amet.
            </details>
        </div>
    </div>
        
    <h3>Kontakt & Öppettider</h3>

    <div id="googleMap">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2031.7364177946968!2d12.010435576674055!3d57.65572884384258!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x464ff2313d740ac1%3A0xd1f57ce2129e0e0a!2sCapio%20V%C3%A5rdcentral%20M%C3%B6lndal!5e1!3m2!1sen!2sse!4v1733819849574!5m2!1sen!2sse" 
        width="50%" height="350" style="border:0;" 
        allowfullscreen="" loading="lazy" 
        referrerpolicy="no-referrer-when-downgrade">
        </iframe>
        <div id="info">
            <strong>Öppettider</strong>
            <p>
                Måndag: 7:30 - 19:00 <br>
                Tisdag: 7:30 - 19:00 <br>
                Onsdag: 7:30 - 19:00 <br>
                Torsdag: 7:30 - 19:00 <br>
                Fredag: 7:30 - 17:00 <br>
                Lördag: Stängt <br>
                Söndag: Stängt <br>
            </p>
            <strong>Kontakt</strong>
            <p>
                Adress: Bergmansgatan 17-23 <br>
                Telefon: 0707-070707 <br>
                Mejl: Kundservice@MölndalVC.se
            </p>
        </div>
    </div>

    

</main>

    
<?php echoFooter(); ?>

</body>
