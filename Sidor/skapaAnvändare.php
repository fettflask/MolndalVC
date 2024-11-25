<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../IMG/favicon.png">
    <link rel="stylesheet" href="../Stylesheets/headerStyle.css">
    <link rel="stylesheet" href="../Stylesheets/skapaStyle.css">
    <title>Registrera</title>
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

        <div class="navbutton" id="push">
            <a href="minaSidor.php">Mina sidor</a>
        </div>

        <div class="navbutton" id="buffer">
            <a href="">Sök vård</a>
        </div>

        <?php
            if(isset($_SESSION["namn"])){
                echo '<div class="navbutton">';
                    echo '<a href="sessionKill.php">Logga ut</a>';
                echo '</div>';
            }
        ?>

    </header>


    <main>
        <?php
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
            $cookiepath = "/tmp/cookies.txt";

            $baseurl= 'http://193.93.250.83:8080/';

            try {
                $ch = curl_init($baseurl.'api/method/login');
            } 
            catch (Exception $e) {
                echo 'Caught exception: ', $e->getMessage(), "\n";
            }

            curl_setopt($ch,CURLOPT_POST, true);
            curl_setopt($ch,CURLOPT_POSTFIELDS, '{"usr":"a23jaced@student.his.se", "pwd":"lmaokraftwerkvem?"}');

            $ch = curl_init($baseurl.'api/resource/Patient');

            if (!empty($_POST)){
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, '{"uid":"'.$_POST["pnr"].'","first_name":"'.$_POST["name"].'","last_name":"'.$_POST["lastname"].'","sex":"'.$_POST["sex"].'"}');
            }

            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
            curl_setopt($ch,CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch,CURLOPT_COOKIEJAR, $cookiepath);
            curl_setopt($ch,CURLOPT_COOKIEFILE, $cookiepath);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

            curl_exec($ch);
            $response = curl_exec($ch);

            $response = json_decode($response,true);
            $error_no = curl_errno($ch);
            $error = curl_error($ch);
            curl_close($ch);
        ?>

        <div id="skapaForm">
            <div id="centerForm">
                <h1>Registrering</h1>
                <form method="POST" action="patientLoggedIn.php">
                    <div class="inputField">
                        Personnummer:
                        <input type="text" name="pnr" class="input" pattern="[0-9]{8}-[0-9]{4}" required maxlength="13" placeholder="YYYYMMDD-XXXX" title="Format: YYYYMMDD-XXXX">
                    </div>
                    <div class="inputField">
                        Förnamn:
                        <input type="text" name="name" class="input" required pattern="[A-Za-zÅåÄäÖö]+" title="Endast bokstäver" placeholder="Förnamn">
                    </div>
                    <div class="inputField">
                        Efternamn:
                        <input  type="text" name="lastname" class="input" required pattern="[A-Za-zÅåÄäÖö]+" title="Endast bokstäver" placeholder="Efternamn">
                    </div>
                    <div id="konSelect">
                        Kön:
                        <select name="sex" id="selector" required title="Välj från listan">
                            <option selected hidden disabled>Välj kön</option>
                                <?php
                                    ini_set("display_errors", 1);
                                    ini_set("display_startup_errors", 1);
                                    error_reporting(E_ALL);
                                    $cookiepath = "/tmp/cookies.txt";

                                    $baseurl = "http://193.93.250.83:8080/";

                                    $ch = curl_init($baseurl . "api/method/login");
                                    curl_setopt($ch, CURLOPT_POST, true);
                                    curl_setopt($ch, CURLOPT_POSTFIELDS, '{usr":"webb_user", "pwd":"Pangolin!24"}');
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
                                    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
                                    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
                                    $login_response = curl_exec($ch);
                                    curl_close($ch);

                                    $ch = curl_init($baseurl . 'api/resource/Gender');
                                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
                                    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
                                    $response = curl_exec($ch);
                                    curl_close($ch);

                                    $genders = json_decode($response, true);

                                    if (isset($genders['data']) && is_array($genders['data'])) {
                                        foreach ($genders['data'] as $gender) {
                                            echo '<option value="' . htmlspecialchars($gender['name']) . '">' . htmlspecialchars($gender['name']) . '</option>';
                                        }
                                    } else {
                                        echo '<option value="">No genders available</option>';
                                        if (isset($genders['message'])) {
                                            echo '<p>Error: ' . htmlspecialchars($genders['message']) . '</p>';
                                        } else {
                                            echo '<p>No gender data available in the response.</p>';
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    <input type="submit" id="registrera" value='Registrera'>
                </form>
            </div>
        </div>
    </main>

    <footer>

    </footer>

    <script>
        function capitalizeInput(event) {
            let input = event.target;
            let value = input.value.trim();

            value = value.replace(/[^a-zA-ZåäöÅÄÖ\s-]/g, '');

            input.value = value.replace(/(^|\s|-)([a-zåäö])/gu, function(match, p1, p2) {
                console.log("Matched Letter:", p2);
                switch (p2) {
                    case 'å': return p1 + 'Å';
                    case 'ä': return p1 + 'Ä';
                    case 'ö': return p1 + 'Ö';
                    default: return p1 + p2.toUpperCase();
                }
            });
        }

        document.getElementById('name').addEventListener('input', capitalizeInput);
        document.getElementById('lastname').addEventListener('input', capitalizeInput);
    </script>
                            
    <script>
        const pnrInput = document.getElementById("pnr");
        pnrInput.addEventListener("input", function () {
            let value = pnrInput.value.replace(/\D/g, '');
            if (value.length > 8) {
                value = value.slice(0, 8) + '-' + value.slice(8, 12);
            }
            pnrInput.value = value;
        });
    </script>
</body>
</html>