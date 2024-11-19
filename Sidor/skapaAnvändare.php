<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <header>

    </header>
    <main>
        <div>
            <p>
                Välkommen till att skapa ett konto som patient hos Mölndals Vårdcentral. Fyll i alla fälten nedanför och identifiera dig med BankID.
            </p>
        </div>
        <div>
            <h3>Patient Inlogg</h3>
            <form method='POST' action='skapaAnvändare.php'>
                <table>
                    <tr>
                        <td>
                            Personnummer:
                        </td>
                        <td>
                            <input type='text' name='pnr'>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Förnamn:
                        </td>
                        <td>
                            <input type='text' name='name'>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Efternamn:
                        </td>
                        <td>
                            <input type='text' name='lastname'>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Kön:
                        </td>
                        <td>
                            <input type='text' name='sex'>
                        </td>
                    </tr>
                </table>
                <input type='submit' value='Öppna BankID'>
            </form>
        </div>

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



            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept:
            application/json'));
            curl_setopt($ch,CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch,CURLOPT_COOKIEJAR, $cookiepath);
            curl_setopt($ch,CURLOPT_COOKIEFILE, $cookiepath);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

            curl_exec($ch);
        ?>
    </main>
    <footer>

    </footer>
</body>
</html>