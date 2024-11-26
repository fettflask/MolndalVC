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

    /**
     * Loggar in på ERP
     * 
     * Används i:
     *    patientLoggedIn
     *    recept
     *    skapaAnvändare
     *    patientJournal
     * 
     * @return void
     */
    function curlSetup(){
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $cookiepath = "/tmp/cookies.txt";

        try {
            $ch = curl_init('http://193.93.250.83:8080/api/method/login');
        } 
        catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

        curl_setopt($ch,CURLOPT_POST, true);

        curl_setopt($ch,CURLOPT_POSTFIELDS, '{"usr":"a23jaced@student.his.se", "pwd":"lmaokraftwerkvem?"}');

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept:
        application/json'));
        curl_setopt($ch,CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch,CURLOPT_COOKIEJAR, $cookiepath);
        curl_setopt($ch,CURLOPT_COOKIEFILE, $cookiepath);
        curl_setopt($ch,CURLOPT_TIMEOUT, $_SESSION["timeout"]);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
    }

?>