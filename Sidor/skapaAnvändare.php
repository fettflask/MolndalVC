<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <header>

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
            curl_setopt($ch,CURLOPT_POSTFIELDS, '{"usr":"webb_user", "pwd":"Pangolin!24"}');

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

        <div>
            <p>
                Välkommen till att skapa ett konto som patient hos Mölndals Vårdcentral. 
                Fyll i alla fälten nedanför och verifiera med BankID.
            </p>
        </div>

        <div>
            <h3>Patient Inlogg</h3>
            <form method="POST" action="patientLoggedIn.php">
                <table>
                    <tr>
                        <td>
                            Personnummer:
                        </td>
                        <td>
                        <input type="text" name="pnr" id="pnr" pattern="[0-9]{8}-[0-9]{4}" required maxlength="13" placeholder="YYYYMMDD-XXXX" title="Format: YYYYMMDD-XXXX">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Förnamn:
                        </td>
                        <td>
                            <input type="text" name="name" id="name" required pattern="[A-Za-zÅåÄäÖö]+" title="Endast bokstäver" placeholder="Förnamn">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Efternamn:
                        </td>
                        <td>
                            <input  type="text" name="lastname" id="lastname" required pattern="[A-Za-zÅåÄäÖö]+" title="Endast bokstäver" placeholder="Efternamn">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Kön:
                        </td>
                        <td>
                            <select name="sex" required title="Välj från listan">
                                <option></option>
                                <?php
                                    ini_set("display_errors", 1);
                                    ini_set("display_startup_errors", 1);
                                    error_reporting(E_ALL);
                                    $cookiepath = "/tmp/cookies.txt";

                                    $baseurl = "http://193.93.250.83:8080/";

                                    $ch = curl_init($baseurl . "api/method/login");
                                    curl_setopt($ch, CURLOPT_POST, true);
                                    curl_setopt($ch, CURLOPT_POSTFIELDS, '{"usr":"webb_user", "pwd":"Pangolin!24"}');
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
                        </td>
                    </tr>
                </table>
                <input type="submit" value=Öppna BankID'>
            </form>
        </div>
    </main>
    <footer>

    </footer>
    <script>
        function capitalizeInput(event) {
            let input = event.target;
            let value = input.value.trim();

            // Remove all non-alphabetical characters except letters and spaces
            value = value.replace(/[^a-zA-ZåäöÅÄÖ\s]/g, '');

            // Capitalize the first letter of each word
            input.value = value.replace(/\b[a-zåäö]/gi, function(match) {
                return match.toUpperCase();
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