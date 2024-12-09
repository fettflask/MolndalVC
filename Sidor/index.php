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
                        <h3>Lorem Ipsum</h3>
                        <p>Aliquam lorem purus, convallis quis turpis et, consectetur malesuada arcu.</p>
                        <!--Lägg adressen här-->
                        <a href="" class="pushdowndammit">
                            <div class="gridbutton">Botono</div>
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
