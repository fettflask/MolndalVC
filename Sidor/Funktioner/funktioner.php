<?php

    function echoHead(){
        echo '
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
    }

?>